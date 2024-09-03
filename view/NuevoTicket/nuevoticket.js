
function init(){
    $("#ticket_form").on("submit",function(e){
        guardaryeditar(e);
    });
}

$(document).ready(function() {
    /* TODO: Inicializar SummerNote */
    $('#tick_descrip').summernote({
        height: 150,
        lang: "es-ES",
        popover: {
            image: [],
            link: [],
            air: []
        },
        callbacks: {
            onImageUpload: function(image) {
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                console.log("Text detect...");
            }
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    /* TODO: Llenar Combo categoria */
    $.post("../../controller/categoria.php?op=combo",function(data, status){
        $('#cat_id').html(data);
    });

    $("#cat_id").change(function(){
        cat_id = $(this).val();
        /* TODO: llenar Combo subcategoria segun cat_id */
        $.post("../../controller/subcategoria.php?op=combo",{cat_id : cat_id},function(data, status){
            $('#cats_id').html(data);
        });
    });

    /* TODO: Llenar combo Prioridad  */
    $.post("../../controller/prioridad.php?op=combo",function(data, status){
        $('#prio_id').html(data);
    });

});

function guardaryeditar(e){
    e.preventDefault();

    $('#btnguardar').prop("disabled",true);
    $('#btnguardar').html('<i class="fa fa-spinner fa-spin"></i> Espere..');

    /* TODO: Array del form ticket */
    var formData = new FormData($("#ticket_form")[0]);
    /* TODO: validamos si los campos tienen informacion antes de guardar */
    if ($('#tick_descrip').summernote('isEmpty') || $('#tick_titulo').val()=='' || $('#cats_id').val() == 0 || $('#cat_id').val() == 0 || $('#prio_id').val() == 0){
        swal("Advertencia!", "Campos Vacios", "warning");
    }else{
        var totalfiles = $('#fileElem').val().length;
        for (var i = 0; i < totalfiles; i++) {
            formData.append("files[]", $('#fileElem')[0].files[i]);
        }

        /* TODO: Guardar Ticket */
        $.ajax({
            url: "../../controller/ticket.php?op=insert",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data){
                console.log("Raw response data:", data);
                try {
                    data = JSON.parse(data);
                    console.log("Parsed data:", data);
                    if (data && data.length > 0 && data[0].tick_id) {
                        swal("Correcto!", "Ticket Registrado Correctamente: Nro-" + data[0].tick_id, "success");
                        $('#tick_titulo').val('');
                        $('#tick_descrip').summernote('reset');
                    } else {
                        swal("Error!", "Ocurrió un error al registrar el ticket", "error");
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    console.error("Server response was:", data);
                    swal("Error!", "Ocurrió un error al intentar registrar el ticket", "error");
                }
                $('#btnguardar').prop("disabled", false);
                $('#btnguardar').html('Guardar');
            }
            
        });
    }
}

init();