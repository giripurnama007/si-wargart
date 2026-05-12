/**
 * SI-WargaRT Main JavaScript
 */

$(document).ready(function() {
    // Initialize DataTables
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/language/id.json"
            }
        });
    }

    if ($('#dataTable2').length) {
        $('#dataTable2').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/language/id.json"
            }
        });
    }

    // Initialize Select2
    if ($('.select2').length) {
        $('.select2').select2();
    }

    // bs-custom-file-input
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }

    // Auto hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation
    $('form').on('submit', function(e) {
        var form = $(this);
        if (form[0].checkValidity() === false) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.addClass('was-validated');
    });

    // Confirm delete
    window.confirmDelete = function(url, message = 'Yakin ingin menghapus data ini?') {
        if (confirm(message)) {
            window.location.href = url;
        }
        return false;
    };

    // AJAX form submission
    window.submitForm = function(formId, url, successCallback) {
        var form = $('#' + formId);
        var formData = new FormData(form[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#loading-overlay').show();
            },
            success: function(response) {
                $('#loading-overlay').hide();
                try {
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            if (successCallback) {
                                successCallback(res);
                            } else if (res.redirect) {
                                window.location.href = res.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                }
            },
            error: function(xhr, status, error) {
                $('#loading-overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error
                });
            }
        });
    };

    // Toast notification
    window.showToast = function(type, message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    // Loading overlay
    $('<div id="loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:9999;text-align:center;padding-top:20%;"><i class="fas fa-spinner fa-spin fa-4x text-primary"></i></div>').appendTo('body');

    // Print function
    window.printPage = function() {
        window.print();
    };

    // Format number
    window.formatNumber = function(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    };

    // Currency format
    window.formatCurrency = function(amount) {
        return 'Rp ' + formatNumber(amount);
    };

    // Date format
    window.formatDate = function(date) {
        var d = new Date(date);
        var day = ('0' + d.getDate()).slice(-2);
        var month = ('0' + (d.getMonth() + 1)).slice(-2);
        var year = d.getFullYear();
        return day + '/' + month + '/' + year;
    };

    // Sidebar active state
    $('.nav-sidebar .nav-item > .nav-link').each(function() {
        var href = $(this).attr('href');
        if (window.location.href.indexOf(href) > -1) {
            $(this).closest('.nav-item').addClass('active');
        }
    });
});

// DataTable defaults
$.extend(true, $.fn.DataTable.defaults, {
    responsive: true,
    autoWidth: false,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    language: {
        lengthMenu: "Tampilkan _MENU_ data",
        zeroRecords: "Tidak ada data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        search: "Cari:",
        paginate: {
            first: "Pertama",
            last: "Terakhir",
            next: ">>",
            previous: "<<"
        }
    }
});
