/**
 * Modern Date Range Picker Initialization
 * Replaces old Flatpickr and Bootstrap Datepicker
 */

"use strict";

$(document).ready(function() {
    'use strict';

    // Initialize all date pickers
    function initDatePickers() {
        // Single date pickers
        $('.single-datepicker').each(function() {
            if (!$(this).data('daterangepicker')) {
                $(this).daterangepicker({
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
            }
        });

        // Date range pickers
        $('.daterangepicker-input').each(function() {
            if (!$(this).data('daterangepicker')) {
                $(this).daterangepicker({
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
        });

        // DateTime range pickers
        $('.datetime-range-picker').each(function() {
            if (!$(this).data('daterangepicker')) {
                $(this).daterangepicker({
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
            }
        });

        // Auto-apply date range pickers
        $('.auto-apply-daterangepicker').each(function() {
            if (!$(this).data('daterangepicker')) {
                $(this).daterangepicker({
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
        });
    }

    // Initialize on page load
    initDatePickers();

    // Re-initialize on AJAX content load
    $(document).on('DOMNodeInserted', function() {
        setTimeout(function() {
            initDatePickers();
        }, 100);
    });

    // Legacy support for old selectors
    $('#flatpickr-date, #invoice-date').each(function() {
        if (!$(this).data('daterangepicker')) {
            $(this).addClass('single-datepicker');
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });
        }
    });

    $('#flatpickr-range, #date-range').each(function() {
        if (!$(this).data('daterangepicker')) {
            $(this).addClass('daterangepicker-input');
            $(this).daterangepicker({
                opens: 'left',
                drops: 'down',
                locale: {
                    format: 'DD-MM-YYYY'
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
    });

    // Bootstrap datepicker legacy support
    $('.input-daterange input').each(function() {
        if (!$(this).data('daterangepicker')) {
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });
        }
    });

    // Event handlers
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
});