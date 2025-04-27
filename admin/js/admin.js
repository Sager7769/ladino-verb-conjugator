(function($) {
    'use strict';

    $(document).ready(function() {
        // טיפול בעריכת פועל
        $('.edit-verb').on('click', function(e) {
            e.preventDefault();
            var verb_id = $(this).data('verb-id');
            window.location.href = 'admin.php?page=ladino-verb-conjugator&action=edit&verb_id=' + verb_id;
        });

        // טיפול במחיקת פועל
        $('.delete-verb').on('click', function(e) {
            e.preventDefault();
            if (confirm('האם אתה בטוח שברצונך למחוק פועל זה?')) {
                var verb_id = $(this).data('verb-id');
                window.location.href = 'admin.php?page=ladino-verb-conjugator&action=delete&verb_id=' + verb_id;
            }
        });

        // טיפול בעריכת זמן
        $('.edit-tense').on('click', function(e) {
            e.preventDefault();
            var tense_id = $(this).data('tense-id');
            window.location.href = 'admin.php?page=ladino-verb-tenses&action=edit&tense_id=' + tense_id;
        });

        // טיפול במחיקת זמן
        $('.delete-tense').on('click', function(e) {
            e.preventDefault();
            if (confirm('האם אתה בטוח שברצונך למחוק זמן זה?')) {
                var tense_id = $(this).data('tense-id');
                window.location.href = 'admin.php?page=ladino-verb-tenses&action=delete&tense_id=' + tense_id;
            }
        });

        // טיפול בטעינת שדות הטיה לזמן נבחר
        $('#tense_select').on('change', function() {
            var tense_id = $(this).val();
            if (tense_id) {
                load_conjugation_fields(tense_id);
            }
        });

        // טעינת שדות ההטיה בעת טעינת הדף
        if ($('#tense_select').length && $('#tense_select').val()) {
            load_conjugation_fields($('#tense_select').val());
        }

        // פונקציה לטעינת שדות הטיה
        function load_conjugation_fields(tense_id) {
            $('#conjugation_fields').html('<p>טוען שדות הטיה...</p>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'load_conjugation_fields',
                    tense_id: tense_id,
                    verb_id: $('#verb_id').val(),
                    nonce: ladinoVerbConjugator.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#conjugation_fields').html(response.data.html);
                    } else {
                        $('#conjugation_fields').html('<p>אירעה שגיאה בטעינת שדות ההטיה.</p>');
                    }
                },
                error: function() {
                    $('#conjugation_fields').html('<p>אירעה שגיאה בטעינת שדות ההטיה.</p>');
                }
            });
        }

        // הוספת טיפול בכפתור ההטיות האוטומטיות
        $('#ladino-auto-conjugate').on('click', function(e) {
            e.preventDefault();
            
            const verbInfinitive = $('#verb_infinitive').val();
            const verbType = $('#verb_type').val();
            
            if (!verbInfinitive || !verbType) {
                alert('אנא הזן את הפועל בצורת המקור ובחר את סוג הפועל תחילה.');
                return;
            }
            
            // הצגת אינדיקטור טעינה
            $(this).prop('disabled', true).text('מעבד...');
            
            // ביצוע קריאת AJAX ליצירת הטיות
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'generate_conjugations',
                    verb_infinitive: verbInfinitive,
                    verb_type: verbType,
                    nonce: ladinoVerbConjugator.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // מילוי כל שדות הזמנים עם ההטיות המוצעות
                        $.each(response.data.conjugations, function(tenseId, conjugationForms) {
                            $.each(conjugationForms, function(person, form) {
                                $(`input[name="conjugation[${tenseId}][${person}]"]`).val(form);
                            });
                        });
                        
                        // הצגת הודעת הצלחה
                        $('#auto-conjugate-message').html('<div class="notice notice-success"><p>ההטיות נוצרו בהצלחה. אנא בדוק ותקן לפי הצורך.</p></div>');
                    } else {
                        // הצגת הודעת שגיאה
                        $('#auto-conjugate-message').html(`<div class="notice notice-error"><p>${response.data.message}</p></div>`);
                    }
                },
                error: function() {
                    // הצגת הודעת שגיאה
                    $('#auto-conjugate-message').html('<div class="notice notice-error"><p>נכשל ביצירת הטיות. אנא נסה שוב.</p></div>');
                },
                complete: function() {
                    // איפוס מצב הכפתור
                    $('#ladino-auto-conjugate').prop('disabled', false).text('הצע הטיות אוטומטיות');
                }
            });
        });
    });

})(jQuery);