var emailreg = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;
var telreg = /^[0-9\-]/;


$('formspan').hide();



$('input').keyup(function() {

    var inputLenght = 4;
    if ($(this).val().length < inputLenght) {
    $(this).parent().addClass('has-error');
    $(this).parent().next('formspan').show()
    $(this).parent().next('formspan').empty();
    $(this).parent().next('formspan').append('<p>Debe ingresar 4 caracteres como mínimo</p>');
    $(this).before().addClass('red');
    $(this).parent().children('i').removeClass('blue');
    $(this).parent().children('i').addClass('red');
    $(this).prev('span').children().removeClass('blue');
    $(this).prev('span').children().addClass('red');
    $('#enviar').next('formspan').show();
    $('#enviar').next('formspan').empty();
    $('#enviar').next('formspan').append('<p>Revise los campos ingresados</p>');
    }else{
    $(this).parent().removeClass('has-error');
    $(this).parent().addClass('has-success');
    $(this).parent().next('formspan').hide();
    $(this).before().removeClass('red');
    $(this).before().addClass('green');
    $(this).prev('span').children().removeClass('red');
    $(this).prev('span').children().addClass('green');
    $('#enviar').removeClass('disabled');
    $('#enviar').next('formspan').hide();
    $('#enviar').next('formspan').empty();
    }
});

$('input').focus(function() {

    var inputLenght = 4;
    if ($(this).val().length < inputLenght) {
    $(this).parent().addClass('has-error');
    $(this).parent().next('formspan').show()
    $(this).parent().next('formspan').empty();
    $(this).parent().next('formspan').append('<p>Debe ingresar 4 caracteres como mínimo</p>');
    $(this).before().addClass('red');
    $(this).parent().children('i').removeClass('blue');
    $(this).parent().children('i').addClass('red');
    $(this).prev('span').children().removeClass('blue');
    $(this).prev('span').children().addClass('red');
    $('#enviar').addClass('disabled');
    $('#enviar').next('formspan').show();
    $('#enviar').next('formspan').empty();
    $('#enviar').next('formspan').append('<p>Revise los campos ingresados</p>');
    }else{
    $(this).parent().removeClass('has-error');
    $(this).parent().addClass('has-success');
    $(this).parent().next('formspan').hide();
    $(this).before().removeClass('red');
    $(this).before().addClass('green');
    $(this).prev('span').children().removeClass('red');
    $(this).prev('span').children().addClass('green');
    $('#enviar').removeClass('disabled');
    $('#enviar').next('formspan').hide();
    $('#enviar').next('formspan').empty();
    }
});

$('#email').keyup(function() {
 if( $("#email").val() == "" ||  !emailreg.test($("#email").val())){
    $(this).parent().addClass('has-error');
    $(this).parent().next('formspan').show()
    $(this).parent().next('formspan').empty();
    $(this).parent().next('formspan').append('<p>Debe ingresar un email válido</p>');
    $(this).before().addClass('red');
    $(this).parent().children('i').removeClass('blue');
    $(this).parent().children('i').addClass('red');
    $(this).prev('span').children().removeClass('blue');
    $(this).prev('span').children().addClass('red');
    }else{
    $(this).parent().removeClass('has-error');
    $(this).parent().addClass('has-success');
    $(this).parent().next('formspan').hide();
    $(this).before().removeClass('red');
    $(this).before().addClass('green');
    $(this).prev('span').children().removeClass('red');
    $(this).prev('span').children().addClass('green');
    }
});

$('#telefono').keyup(function() {
 if( $("#telefono").val() == "" ||  !telreg.test($("#telefono").val())){
    $(this).parent().addClass('has-error');
    $(this).parent().next('formspan').show()
    $(this).parent().next('formspan').empty();
    $(this).parent().next('formspan').append('<p>Debe ingresar un teléfono válido</p>');
    $(this).before().addClass('red');
    $(this).parent().children('i').removeClass('blue');
    $(this).parent().children('i').addClass('red');
    $(this).prev('span').children().removeClass('blue');
    $(this).prev('span').children().addClass('red');
    }else{
    $(this).parent().removeClass('has-error');
    $(this).parent().addClass('has-success');
    $(this).parent().next('formspan').hide();
    $(this).before().removeClass('red');
    $(this).before().addClass('green');
    $(this).prev('span').children().removeClass('red');
    $(this).prev('span').children().addClass('green');
    }


});


$('body').mousemove(function(){

    var element = $('.form-control');

    for (i=0;i<element.length;i++){ 
    var test = element[i];
    //console.log(typeof test.value);
    var aver = test.value;
    //console.log(aver)
   if ( aver == '') {
            $('#enviar').addClass('disabled');
    $('#enviar').next('formspan').show();
    $('#enviar').next('formspan').empty();
    $('#enviar').next('formspan').append('<p>Hay campos vacíos</p>'); 
   };
    
}
});

$('body').keyup(function(){

    var element = $('.form-control');

    for (i=0;i<element.length;i++){ 
    var test = element[i];
    //console.log(typeof test.value);
    var aver = test.value;
    //console.log(aver)
   if ( aver == '') {
            $('#enviar').addClass('disabled');
    $('#enviar').next('formspan').show();
    $('#enviar').next('formspan').empty();
    $('#enviar').next('formspan').append('<p>Hay campos vacíos</p>'); 
   };
    
}
});

$('.editar').mousemove(function(){

    var element = $('.form-control');

    for (i=0;i<element.length;i++){ 
    var test = element[i];
    //console.log(typeof test.value);
    var aver = test.value;
    //console.log(aver)
   if ( aver == '') {
            $('#enviar').addClass('disabled');
    $('#enviar').next('formspan').show();
    $('#enviar').next('formspan').empty();
    $('#enviar').next('formspan').append('<p>Hay campos vacíos</p>'); 
   };
    
}
});

