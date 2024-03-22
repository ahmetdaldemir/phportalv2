/**
 * Custom
 */

'use strict';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function updateStatus(url, id, data,field) {


    $.post(url,
        {
            id: id,
            is_status: data,
        },
        function (data, status) {
            if (data == 1) {
                $.MessageBox("Güncellendi");
            } else {
                $.MessageBox("Sorun Var");
            }

        });
}

function updateField(url,id, data,field) {


    var datas = (id, field, value) => {
        var data = {
            id: id
        };

        data[field] = value; // Dinamik olarak field adını kullanarak value değerini atama

        return data;
    };
    var myData = datas(id, data,field);

    console.log(datas);
    $.ajax({
        type: "POST",
        url: url,
        data: myData,
        success: function (response) {
            $("#result").empty().append(response);
        }
    });
}
function updateField(url, id, data,field) {


    $.post(url,
        {
            id: id,
            field: data,
        },
        function (data, status) {
            if (data == 1) {
                $.MessageBox("Güncellendi");
            } else {
                $.MessageBox("Sorun Var");
            }

        });
}


function updateTechnical(url, id, data) {


    $.post(url,
        {
            id: id,
            technical: data,
        },
        function (data, status) {
            if (data == 1) {
                $.MessageBox("Güncellendi");
            } else {
                $.MessageBox("Sorun Var");
            }

        });
}

function updateDanger(url, id, data) {


    $.post(url,
        {
            id: id,
            is_danger: data,
        },
        function (data, status) {
            if (data == 1) {
                $.MessageBox("Güncellendi");
            } else {
                $.MessageBox("Sorun Var");
            }

        });
}

function saveStockMovement(url) {
    $.ajax({
        type: "POST",
        url: url,
        data: $("#stockmovementform").serialize(),
        success: function (response) {
            $("#result").empty().append(response);
        }
    });
}

function getTown(sel) {
    var postUrl = window.location.origin + '/get_cities?id=' + sel + '';   // Returns base URL (https://example.com)
    $.ajax({
        type: "GET",
        url: postUrl,
        success: function (response) {
            $.each(response, function (index, value) {
                $('#district').append($('<option>', {
                    value: value.id,
                    text: value.name
                }));
            });
        }
    });
}


function customerSave() {
    var postUrl = window.location.origin + '/custom_customerstore';   // Returns base URL (https://example.com)
    var formData = $("#customerForm").serialize();
    $.ajax({
        type: "POST",
        url: postUrl,
        data: formData,
        dataType: "json",
        encode: true,
    }).done(function (data) {

        $(".customerinformation").html('<p className="mb-1">\'+data.address+\'</p>\n' +
            '<p className="mb-1">\'+data.phone1+\'</p>');

        var modalDiv = $("#editUser");
        modalDiv.modal('hide');
        modalDiv
            .find("input,textarea,select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    });

}


function saveEInvoice(url) {
    $.ajax({
        type: "POST",
        url: url,
        data: $("#sellForm").serialize(),
        success: function (response) {
            console.log(response);
        }
    });

}

function getStock() {
    var postUrl = window.location.origin + '/getStock';   // Returns base URL (https://example.com)
    var formData = $("#stockForm").serialize();
    $.ajax({
        type: "POST",
        url: postUrl,
        data: formData,
        dataType: "json",
        encode: true,
    }).done(function (data) {
        console.log(data);

    });

}


function getVersion(sel) {
    var postUrl = window.location.origin + '/get_version?id=' + sel + '';   // Returns base URL (https://example.com)
    $("#version_id").html('');
    $.ajax({
        type: "GET",
        url: postUrl,
        success: function (response) {
            $('#version_id').append('<option value="">Tümü</option>');
            $.each(response, function (index, value) {
                $('#version_id').append($('<option>', {
                    value: value.id,
                    text: value.name
                }));
            });
        }
    });
}


$(document).ready(function() {
    var sel = $('#brand_id').find(":selected").val();
    console.log(sel);
    var postUrl = window.location.origin + '/get_version?id=' + sel + '';   // Returns base URL (https://example.com)
    $.ajax({
        type: "GET",
        url: postUrl,
        success: function (response) {
            $.each(response, function (index, value) {
                $('#version_id').append($('<option>', {
                    value: value.id,
                    text: value.name,
                    selected:getSelect(value.id),
                }));
            });
        }
    });

    function getSelect(id)
    {
        var version = $('#version_id').data('version');
        var type = typeof version;
        if(type === "number")
        {
            var newversion = version.toString();
            if(newversion.search(id) !== -1){ return true; }
        }else{
            if(version.search(id) !== -1){ return true; }
        }
    }
});
