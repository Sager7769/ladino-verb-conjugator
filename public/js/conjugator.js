/**
 * JavaScript עבור מנוע הטיית פעלים בלאדינו בצד הלקוח
 * 
 * @package Ladino_Verb_Conjugator
 */
(function($) {
    'use strict';
    
    // הגדרות גלובליות
    const conjugator = {
        searchInput: null,
        resultsContainer: null,
        displayContainer: null,
        showAllBtn: null,
        loadingIndicator: null,
        requestTimeout: null,
        requestCount: 0,
        maxRequests: 30, // הגבלת מספר בקשות עוקבות
        
        /**
         * אתחול המנוע
         */
        init: function() {
            // בחירת האלמנטים בדף
            this.searchInput = $('#ladino-verb-search');
            this.resultsContainer = $('#ladino-search-results');
            this.displayContainer = $('#ladino-conjugation-display');
            this.showAllBtn = $('#ladino-show-all-verbs');
            
            // יצירת מחוון טעינה
            this.loadingIndicator = $('<div class="ladino-loading"><span class="ladino-spinner"></span></div>');
            this.loadingIndicator.insertAfter(this.searchInput);
            this.loadingIndicator.hide();
            
            // אתחול אירועים
            this.setupEvents();
            
            // בדיקה אם יש פרמטר של פועל ב-URL
            this.checkUrlParam();
        },
        
        /**
         * אתחול אירועים
         */
        setupEvents: function() {
            // חיפוש בעת הקלדה
            this.searchInput.on('input', this.debounce(this.searchVerbs.bind(this), 300));
            
            // חיפוש בעת לחיצה על Enter
            this.searchInput.on('keydown', function(e) {
                if (e.keyCode === 13) { // מקש Enter
                    e.preventDefault();
                    this.searchVerbs();
                }
            }.bind(this));
            
            // הצגת כל הפעלים
            this.showAllBtn.on('click', this.showAllVerbs.bind(this));
            
            // בחירת פועל מהרשימה
            this.resultsContainer.on('click', '.ladino-verb-item', this.selectVerb.bind(this));
            
            // התנהגות רספונסיבית
            $(window).on('resize', this.debounce(this.checkResponsiveLayout.bind(this), 200));
            this.checkResponsiveLayout();
        },
        
        /**
         * בדיקה אם יש פרמטר של פועל ב-URL
         */
        checkUrlParam: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const verb = urlParams.get('verb');
            
            if (verb) {
                this.searchInput.val(verb);
                this.searchVerbs();
            }
        },
        
        /**
         * חיפוש פעלים
         */
        searchVerbs: function() {
            const term = this.searchInput.val().trim();
            
            if (term.length < 2) {
                this.resultsContainer.empty();
                return;
            }
            
            // מניעת בקשות רבות מדי
            if (this.requestCount >= this.maxRequests) {
                this.displayError(ladino_conjugator_i18n.too_many_requests);
                setTimeout(() => {
                    this.requestCount = 0;
                }, 60000); // איפוס אחרי דקה
                return;
            }
            
            // הגדלת מונה הבקשות
            this.requestCount++;
            
            // הצגת מחוון טעינה
            this.loadingIndicator.show();
            
            // ניקוי תוצאות קודמות
            this.resultsContainer.empty();
            
            // שמירת הפרמטר בהיסטוריית הדפדפן ללא טעינה מחדש של הדף
            const url = new URL(window.location);
            url.searchParams.set('verb', term);
            window.history.pushState({}, '', url);
            
            $.ajax({
                url: ladino_conjugator_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'ladino_search_verbs',
                    nonce: ladino_conjugator_data.nonce,
                    search: term
                },
                success: function(response) {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    if (response.success && response.data) {
                        // הצגת תוצאות החיפוש
                        this.displaySearchResults(response.data);
                        
                        // אם יש תוצאה אחת בלבד, בחר אותה אוטומטית
                        if (response.data.length === 1) {
                            this.displayConjugation(response.data[0]);
                            this.resultsContainer.empty();
                        }
                        
                        // אם יש התאמה מדויקת, בחר אותה
                        const exactMatch = response.data.find(verb => 
                            verb.infinitive === term || verb.translation === term
                        );
                        
                        if (exactMatch) {
                            this.displayConjugation(exactMatch);
                            this.resultsContainer.empty();
                        }
                    } else if (response.success && response.data.length === 0) {
                        // אם אין תוצאות
                        this.displayNotFound(term);
                    } else {
                        // אם יש שגיאה
                        this.displayError(response.data || ladino_conjugator_i18n.error_occurred);
                    }
                }.bind(this),
                error: function(xhr, status, error) {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    // טיפול בשגיאות HTTP
                    if (xhr.status === 429) {
                        this.displayError(ladino_conjugator_i18n.too_many_requests);
                    } else {
                        this.displayError(ladino_conjugator_i18n.error_occurred + ': ' + error);
                    }
                }.bind(this)
            });
        },
        
        /**
         * הצגת תוצאות חיפוש
         */
        displaySearchResults: function(verbs) {
            this.resultsContainer.empty();
            
            if (verbs.length === 0) {
                this.resultsContainer.html('<p class="ladino-no-results">' + 
                    ladino_conjugator_i18n.no_results + '</p>');
                return;
            }
            
            const list = $('<ul class="ladino-verb-list"></ul>');
            
            verbs.forEach(function(verb) {
                const item = $('<li class="ladino-verb-item" data-id="' + verb.id + '">' +
                    '<span class="ladino-verb-infinitive">' + verb.infinitive + '</span>' +
                    ' - ' +
                    '<span class="ladino-verb-translation">' + verb.translation + '</span>' +
                    '</li>');
                list.append(item);
            });
            
            this.resultsContainer.append(list);
        },
        
        /**
         * הצגת הטיות הפועל
         */
        displayConjugation: function(verb) {
            // פענוח ההטיות אם צריך
            if (typeof verb.conjugations === 'string') {
                try {
                    verb.conjugations = JSON.parse(verb.conjugations);
                } catch (e) {
                    this.displayError(ladino_conjugator_i18n.invalid_data);
                    return;
                }
            }
            
            const persons = {
                singular: {
                    first: ladino_conjugator_i18n.i,
                    second: ladino_conjugator_i18n.you_singular,
                    third: ladino_conjugator_i18n.he_she
                },
                plural: {
                    first: ladino_conjugator_i18n.we,
                    second: ladino_conjugator_i18n.you_plural,
                    third: ladino_conjugator_i18n.they
                }
            };
            
            // יצירת תצוגת ההטיות
            const display = $('<div class="ladino-verb-display"></div>');
            
            // כותרת ותרגום
            display.append('<div class="ladino-verb-header">' +
                '<h3 class="ladino-verb-title">' + verb.infinitive + '</h3>' +
                '<p class="ladino-verb-meaning">' + verb.translation + '</p>' +
                '</div>');
            
            // האם לשתף
            display.append('<div class="ladino-share-links">' +
                '<button class="ladino-share-button" data-verb="' + verb.infinitive + '">' +
                '<i class="ladino-icon-share"></i> ' + ladino_conjugator_i18n.share +
                '</button>' +
                '</div>');
            
            // הטיות בזמנים
            const tensesGrid = $('<div class="ladino-tenses-grid"></div>');
            
            // לולאה על כל הזמנים
            Object.keys(verb.conjugations).forEach(function(tense) {
                const tenseBlock = $('<div class="ladino-tense-block"></div>');
                
                // כותרת הזמן
                const tenseTitle = ladino_conjugator_i18n.tenses[tense] || tense;
                tenseBlock.append('<h4 class="ladino-tense-title">' + tenseTitle + '</h4>');
                
                // טבלת ההטיות
                const table = $('<table class="ladino-conjugation-table"></table>');
                
                // יצירת שורות עבור כל גוף
                Object.keys(persons).forEach(function(number) {
                    Object.keys(persons[number]).forEach(function(person) {
                        const row = $('<tr></tr>');
                        
                        // שם הגוף
                        row.append('<td class="ladino-person">' + persons[number][person] + '</td>');
                        
                        // צורת ההטיה
                        let form = '';
                        try {
                            form = verb.conjugations[tense][number][person] || '-';
                        } catch (e) {
                            form = '-';
                        }
                        
                        row.append('<td class="ladino-form">' + form + '</td>');
                        table.append(row);
                    });
                });
                
                tenseBlock.append(table);
                tensesGrid.append(tenseBlock);
            });
            
            display.append(tensesGrid);
            
            // הוספת הערה לגבי זמנים נוספים
            if (ladino_conjugator_data.show_notes) {
                display.append('<div class="ladino-note">' +
                    '<p>' + ladino_conjugator_i18n.tenses_note + '</p>' +
                    '</div>');
            }
            
            // אירועי שיתוף
            display.find('.ladino-share-button').on('click', function(e) {
                const verb = $(e.currentTarget).data('verb');
                this.shareVerb(verb);
            }.bind(this));
            
            this.displayContainer.html(display);
        },
        
        /**
         * שיתוף פועל
         */
        shareVerb: function(verb) {
            // יצירת טקסט לשיתוף
            const shareText = ladino_conjugator_i18n.share_text
                .replace('%s', verb)
                .replace('%u', window.location.href);
            
            // בדיקה אם Web Share API זמין
            if (navigator.share) {
                navigator.share({
                    title: ladino_conjugator_i18n.share_title,
                    text: shareText,
                    url: window.location.href
                }).catch(function(error) {
                    console.error('Error sharing:', error);
                });
            } else {
                // עותק לקליפבורד אם Web Share API לא זמין
                const textArea = document.createElement('textarea');
                textArea.value = shareText;
                textArea.style.position = 'fixed';
                textArea.style.opacity = 0;
                document.body.appendChild(textArea);
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    this.showToast(ladino_conjugator_i18n.copied_to_clipboard);
                } catch (err) {
                    console.error('Error copying text:', err);
                }
                
                document.body.removeChild(textArea);
            }
        },
        
        /**
         * הצגת הודעה צפה
         */
        showToast: function(message) {
            const toast = $('<div class="ladino-toast">' + message + '</div>');
            $('body').append(toast);
            
            setTimeout(function() {
                toast.addClass('ladino-toast-show');
            }, 100);
            
            setTimeout(function() {
                toast.removeClass('ladino-toast-show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        },
        
        /**
         * הצגת הודעה כאשר הפועל לא נמצא
         */
        displayNotFound: function(term) {
            this.displayContainer.html(
                '<div class="ladino-verb-not-found">' +
                '<h3>' + ladino_conjugator_i18n.verb_not_found.replace('%s', term) + '</h3>' +
                '<p>' + ladino_conjugator_i18n.try_another_search + '</p>' +
                '</div>'
            );
        },
        
        /**
         * הצגת הודעת שגיאה
         */
        displayError: function(message) {
            this.displayContainer.html(
                '<div class="ladino-error">' +
                '<h3>' + ladino_conjugator_i18n.error + '</h3>' +
                '<p>' + message + '</p>' +
                '</div>'
            );
        },
        
        /**
         * הצגת כל הפעלים
         */
        showAllVerbs: function() {
            // הצגת מחוון טעינה
            this.loadingIndicator.show();
            
            $.ajax({
                url: ladino_conjugator_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'ladino_get_all_verbs',
                    nonce: ladino_conjugator_data.nonce
                },
                success: function(response) {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    if (response.success && response.data) {
                        // ניקוי תצוגת הפועל
                        this.displayContainer.empty();
                        
                        // הצגת כל הפעלים
                        this.displaySearchResults(response.data);
                    } else {
                        this.displayError(ladino_conjugator_i18n.error_occurred);
                    }
                }.bind(this),
                error: function() {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    this.displayError(ladino_conjugator_i18n.error_occurred);
                }.bind(this)
            });
        },
        
        /**
         * בחירת פועל מהרשימה
         */
        selectVerb: function(e) {
            const id = $(e.currentTarget).data('id');
            
            // הצגת מחוון טעינה
            this.loadingIndicator.show();
            
            $.ajax({
                url: ladino_conjugator_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'ladino_get_verb',
                    nonce: ladino_conjugator_data.nonce,
                    id: id
                },
                success: function(response) {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    if (response.success && response.data) {
                        // הצגת ההטיות של הפועל
                        this.displayConjugation(response.data);
                        
                        // ניקוי תוצאות החיפוש
                        this.resultsContainer.empty();
                        
                        // עדכון תיבת החיפוש
                        this.searchInput.val(response.data.infinitive);
                        
                        // עדכון הפרמטר בהיסטוריית הדפדפן
                        const url = new URL(window.location);
                        url.searchParams.set('verb', response.data.infinitive);
                        window.history.pushState({}, '', url);
                    } else {
                        this.displayError(ladino_conjugator_i18n.error_occurred);
                    }
                }.bind(this),
                error: function() {
                    // הסתרת מחוון טעינה
                    this.loadingIndicator.hide();
                    
                    this.displayError(ladino_conjugator_i18n.error_occurred);
                }.bind(this)
            });
        },
        
        /**
         * התאמת הפריסה למסכים קטנים
         */
        checkResponsiveLayout: function() {
            if (window.innerWidth < 768) {
                $('.ladino-conjugator-container').addClass('ladino-mobile');
            } else {
                $('.ladino-conjugator-container').removeClass('ladino-mobile');
            }
        },
        
        /**
         * פונקציית השהיה
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // אתחול הפלאגין בטעינת הדף
    $(document).ready(function() {
        conjugator.init();
    });
    
})(jQuery);