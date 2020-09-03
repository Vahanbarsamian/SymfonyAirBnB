$(function(){
    $.each( $("input[id$='_url']").hover(
    function (e) {
        var image = "<div class='float-right' id='image'><img src='"+$(this).val()+"' alt='' class='img-thumbnail'></div>";
        $(image).appendTo($(this).parent());
    },
    function(e){
        $('#image').remove();
    }
    ),function(){});
});