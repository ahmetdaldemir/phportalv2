/**
 * Date Range Picker Initialization
 * Modern jQuery Date Range Picker implementation
 */

$(document).ready(function() {
    'use strict';

    // Initialize Date Range Picker
    function initDateRangePicker() {
        // Basic date range picker
        $('.daterangepicker-input').daterangepicker({
            opens: 'left',
            drops: 'down',
            locale: {
                direction: 'ltr',
                format: 'DD-MM-YYYY',
                separator: ' - ',
                applyLabel: 'Uygula',
                cancelLabel: 'İptal',
                weekLabel: 'H',
                customRangeLabel: 'Özel Aralık',
                daysOfWeek: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                firstDay: 1
            },
            ranges: {
                'Bugün': [moment(), moment()],
                'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
                'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
                'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        });

        // Single date picker
        $('.single-datepicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            opens: 'left',
            drops: 'down',
            locale: {
                direction: 'ltr',
                format: 'DD-MM-YYYY',
                separator: ' - ',
                applyLabel: 'Uygula',
                cancelLabel: 'İptal',
                weekLabel: 'H',
                customRangeLabel: 'Özel Aralık',
                daysOfWeek: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                firstDay: 1
            }
        });

        // Time picker with date range
        $('.datetime-range-picker').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            locale: {
                direction: 'ltr',
                format: 'DD-MM-YYYY HH:mm',
                separator: ' - ',
                applyLabel: 'Uygula',
                cancelLabel: 'İptal',
                weekLabel: 'H',
                customRangeLabel: 'Özel Aralık',
                daysOfWeek: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                firstDay: 1
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        });

        // Auto-apply date range picker
        $('.auto-apply-daterangepicker').daterangepicker({
            autoApply: true,
            opens: 'left',
            drops: 'down',
            locale: {
                direction: 'ltr',
                format: 'DD-MM-YYYY',
                separator: ' - ',
                applyLabel: 'Uygula',
                cancelLabel: 'İptal',
                weekLabel: 'H',
                customRangeLabel: 'Özel Aralık',
                daysOfWeek: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                firstDay: 1
            },
            ranges: {
                'Bugün': [moment(), moment()],
                'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
                'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
                'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        });
    }

    // Initialize on page load
    initDateRangePicker();

    // Re-initialize on AJAX content load
    $(document).on('DOMNodeInserted', function() {
        setTimeout(function() {
            $('.daterangepicker-input:not([data-daterangepicker-initialized])').each(function() {
                $(this).attr('data-daterangepicker-initialized', 'true');
                initDateRangePicker();
            });
        }, 100);
    });

    // Custom event handlers
    $(document).on('apply.daterangepicker', function(ev, picker) {
        console.log('Date range applied:', picker.startDate.format('DD-MM-YYYY'), 'to', picker.endDate.format('DD-MM-YYYY'));
        
        // Trigger custom event for other components
        $(document).trigger('daterangepicker:applied', {
            startDate: picker.startDate,
            endDate: picker.endDate,
            element: picker.element
        });
    });

    $(document).on('cancel.daterangepicker', function(ev, picker) {
        console.log('Date range picker cancelled');
        
        // Trigger custom event for other components
        $(document).trigger('daterangepicker:cancelled', {
            element: picker.element
        });
    });

    // Utility functions
    window.DateRangePickerUtils = {
        // Get current date range
        getCurrentRange: function(selector) {
            var element = $(selector);
            if (element.length && element.data('daterangepicker')) {
                var picker = element.data('daterangepicker');
                return {
                    startDate: picker.startDate,
                    endDate: picker.endDate
                };
            }
            return null;
        },

        // Set date range programmatically
        setDateRange: function(selector, startDate, endDate) {
            var element = $(selector);
            if (element.length && element.data('daterangepicker')) {
                var picker = element.data('daterangepicker');
                picker.startDate = moment(startDate);
                picker.endDate = moment(endDate);
                picker.updateView();
            }
        },

        // Clear date range
        clearDateRange: function(selector) {
            var element = $(selector);
            if (element.length) {
                element.val('');
                if (element.data('daterangepicker')) {
                    element.data('daterangepicker').remove();
                }
            }
        }
    };
});
