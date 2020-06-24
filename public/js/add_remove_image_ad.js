$(document).ready(function(){
    
// Add an image when newImage addbutton is typed

    $(document).on('click','#btn-add',function(){
        let tmpl = $('#ad_images').data('prototype').replace(/__name__/g, count());
        $('#ad_images').append(tmpl);
        count();
    });
    
// Delete the image choosed by the  user typing delete button

    $(document).on('click','.btn-delete',function(e){
            let result = confirm('Confirmez-vous la suppression de cette image ?');
                if (result == true){
                    if ($(this).closest('fieldset.form-group').attr('id')){
                        $(this).closest('fieldset.form-group').remove();
                    } else {
                        $('fieldset.form-group:last').remove();
                    }
                }
                count();
    });

    function count(){
        let nb = $('#ad_images .row').length;
        for (let i=0;i<=nb;i++) {
            if (!$('fieldset.form-group:nth-child('+i+')').attr('id')) {
                $('fieldset.form-group:nth-child('+i+')').attr('id', "block_"+i);
            }else{
                $('fieldset.form-group:nth-child('+i+')').attr('id', "block_"+i);
            }
        }
        return nb;
    }
    count();
});