$(document).ready(function() {
    $("#updates").slideToggle();
    $('#kopf').slideToggle();
    $.post('index2.php', {
        series: 1
    }, function (output) {
        $('#series').html(output).show();
    });
    $.post('../sql/getUpdates.php',  function (output) {
        $('#comments').html(output);
    });
    $('input:text, input:password, input:button, input[type=email]').button().addClass('ui-textfield');
    $('a.login-window').click(function() {
		
        //Getting the variable's value from a link 
        var loginBox = $(this).attr('href');

        //Fade in the Popup
        $(loginBox).fadeIn(300);
		
        //Set the center alignment padding + border see css style
        var popMargTop = ($(loginBox).height() + 24) / 2; 
        var popMargLeft = ($(loginBox).width() + 24) / 2; 
		
        $(loginBox).css({ 
            'margin-top' : -popMargTop,
            'margin-left' : -popMargLeft
        });
		
        // Add the mask to body
        $('body').append('<div id="mask"></div>');
        $('#mask').fadeIn(300);
		
        return false;
    });
	
    // When clicking on the button close or the mask layer the popup closed
    $('a.close, #mask').live('click', function() { 
        $('#mask , .login-popup').fadeOut(300 , function() {
            $('#mask').remove();  
        }); 
        return false;
    });
});

function get(ID) { 
    $('#series').toggle('drop', {
        direction: 'left'
    }, 500);
    $.post('Series.php', {
        series: ID
    }, function (output) {
        $('#volumes').html(output).toggle('drop', {
            direction: 'right'
        }, 1000);
    }); 
    $('#b2i').show('drop', {
        direction: 'left'
    }, 1000);
}

function getBook(ID) { 
    $('#volumes').toggle('drop', {
        direction: 'left'
    }, 500);
    $.post('Book.php', {
        book: ID
    }, function (output) {
        $('#book').html(output).toggle('drop', {
            direction: 'right'
        }, 1000);
    });
    $('#b2s').show('drop', {
        direction: 'left'
    }, 1000);
}
    
function searchBook(ID,SID) { 
    $("#quickfind").val('');
    $('#volumes:visible').toggle('drop', {
        direction: 'left'
    }, 500);
    $('#series:visible').toggle('drop', {
        direction: 'left'
    }, 500);
    $.post('Series.php', {
        series: SID
    }, function (output) {
        $('#volumes').html(output);
    }); 
    $.post('Book.php', {
        book: ID
    }, function (output) {
        $('#book:hidden').html(output).toggle('drop', {
            direction: 'right'
        }, 1000);
        $('#book:visible').html(output);
    });
    $('#b2i').show('drop', {
        direction: 'left'
    }, 1000);
    $('#b2s').show('drop', {
        direction: 'left'
    }, 1000);
    $.mask.close();
}

$("#b2i").click(function() {
    $('#book:visible').toggle('drop', {
        direction: 'right'
    }, 500); 
    $('#volumes:visible').toggle('drop', {
        direction: 'right'
    }, 500);
    $('#series:hidden').toggle('drop', {
        direction: 'left'
    }, 1000); 
    $('#b2i').hide('drop', {
        direction: 'left'
    }, 500); 
    $('#b2s:visible').hide('drop', {
        direction: 'left'
    }, 500);
});

$("#b2s").click(function() {
    $('#book:visible').toggle('drop', {
        direction: 'right'
    }, 500);
    $('#volumes:hidden').toggle('drop', {
        direction: 'left'
    }, 1000);
    $('#b2s').hide('drop', {
        direction: 'left'
    }, 500);
});

$("#updates").click( function () {
    $.post('../sql/getUpdates.php',  function (output) {
        $('#comments').html(output);
    });
    $("#comments").expose({
    
        color: "black",

        onBeforeLoad: function() {
            $("#comments").slideToggle("slow");
        },
 
        onBeforeClose: function() {
            $("#comments").slideToggle("slow");
        }
    });
});

$("#quickfind").autocomplete({
    source: "../sql/search.php",
    minLength: 2,//search after two characters
    select: function(event,ui){      
        searchBook(ui.item.id, ui.item.sid);
    }
});