$(document).ready(function(){
    if(null === localStorage.getItem('rememberMe')) window.localStorage.setItem('rememberMe','false');
    
    
    function loadId(){
        if ($('#check-login').is(':checked')){
            let val = $('#loginName').val();
            if(val.length>0){
                window.localStorage.setItem('newVal', val);
                localStorage.setItem('rememberMe','true');
                return true;
            }
        }else{
            localStorage.setItem('rememberMe','false');
            return false;
        }
    }
    
    function disabled(){
        let authorized = $('#loginName').val().length > 0 ? false : true;
        $('#check-login').attr("disabled", authorized ).prop('checked', loadId());
        return authorized;
    }
    
    $('#check-login').on('click', function(){ loadId(); });
    $('#loginName').keyup(function (e) { 
        disabled();
    });
    
    if(localStorage.getItem('rememberMe') === 'true'){
        val = window.localStorage.getItem('newVal');
        if (val.length>0){
            $('#loginName').val(val);
            $('#check-login').prop('checked','checked');
        }
    }else{
        $('#check-login').prop('checked',false);
        $('#loginName').val("");
    }
    
    disabled(); 
});


