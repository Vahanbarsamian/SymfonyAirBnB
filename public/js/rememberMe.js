$(document).ready(function(){
    val = $('input#loginName').attr('value');
    if(null === localStorage.getItem('rememberMe')) window.localStorage.setItem('rememberMe','false');
    $('#check-login').on('change',function(){
        if ($(this).is(':checked')){
            if ('' !== val){
                $('#loginName').val(val);
                window.localStorage.setItem('newVal', val);
            }
            localStorage.setItem('rememberMe','true');
        }else{
            localStorage.setItem('rememberMe','false');
        }
    });
    if(localStorage.getItem('rememberMe') === 'true'){
        val = window.localStorage.getItem('newVal');
        $('#loginName').val(val);
        $('#check-login').prop('checked','checked');
    }else{
        $('#check-login').prop('checked',false);
    }
});
