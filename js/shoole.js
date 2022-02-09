/**
 * Created by albert on 10/4/15.
 */
( function( $ ) {
    $( 'a[href="#"]' ).click( function(e) {
        e.preventDefault();
    } );
} )( jQuery );

function test(val)
{
    $('#'+val).addClass('btn-primary');
}

/*LEFT NAV */
$('#leftnav ol>li:has(ol)>span').addClass('glyphicon-chevron-down');

$('#leftnav ol>li>a[href=""]').click(function(e){
    e.preventDefault();

    var checkElement = $(this).next().next();

    $('#leftnav li').removeClass('live');
    $('#leftnav li').addClass('normal');
    $(this).closest('li').removeClass('normal');
    $(this).closest('li').addClass('live');
    $(this).closest('li:has(ol)').removeClass('live')
    $(this).closest('li:has(ol)').addClass('normal');

    if((checkElement.is('ol')) && (checkElement.is(':visible')))  {
        $(this).next().removeClass('glyphicon-chevron-up');
        $(this).next().addClass('glyphicon-chevron-down');
        checkElement.slideUp('normal');
    }

    if((checkElement.is('ol')) && ! (checkElement.is(':visible')))  {
        checkElement.slideDown('normal');
        $(this).next().removeClass('glyphicon-chevron-down');
        $(this).next().addClass('glyphicon-chevron-up');
    }


});
$('#leftnav ol>li:has(ol)>span').click(function(e){
    e.preventDefault();

    var checkElement = $(this).next();

    $('#leftnav li').removeClass('live');
    $('#leftnav li').addClass('normal');
    $(this).closest('li').removeClass('normal');
    $(this).closest('li').addClass('live');
    $(this).closest('li:has(ol)').removeClass('live')
    $(this).closest('li:has(ol)').addClass('normal');

    if((checkElement.is('ol')) && (checkElement.is(':visible')))  {
        $(this).removeClass('glyphicon-chevron-up');
        $(this).addClass('glyphicon-chevron-down');
        checkElement.slideUp('normal');
    }

    if((checkElement.is('ol')) && ! (checkElement.is(':visible')))  {
        checkElement.slideDown('normal');
        $(this).removeClass('glyphicon-chevron-down');
        $(this).addClass('glyphicon-chevron-up');
    }


});


/*RIGHT NAV*/

$('#rightnav ul>li:has(ul)>span').addClass('glyphicon-plus-sign');

$('#rightnav ul>li>a[href=""]').click(function(e){
    e.preventDefault();

    var checkElement = $(this).next().next();

    if((checkElement.is('ul')) && (checkElement.is(':visible')))  {
        $(this).next().removeClass('glyphicon-minus-sign');
        $(this).next().addClass('glyphicon-plus-sign');
        checkElement.slideUp('normal');
    }

    if((checkElement.is('ul')) && ! (checkElement.is(':visible')))  {
        checkElement.slideDown('normal');
        $(this).next().removeClass('glyphicon-plus-sign');
        $(this).next().addClass('glyphicon-minus-sign');
    }


});
$('#rightnav ul>li:has(ul)>span').click(function(e){
    e.preventDefault();

    var checkElement = $(this).next();

    if((checkElement.is('ul')) && (checkElement.is(':visible')))  {
        $(this).removeClass('glyphicon-minus-sign');
        $(this).addClass('glyphicon-plus-sign');
        checkElement.slideUp('normal');
    }

    if((checkElement.is('ul')) && ! (checkElement.is(':visible')))  {
        checkElement.slideDown('normal');
        $(this).removeClass('glyphicon-plus-sign');
        $(this).addClass('glyphicon-minus-sign');
    }


});
