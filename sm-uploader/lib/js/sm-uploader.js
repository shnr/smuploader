var parentCl;
var currentlength; // current list amount
var updated_flag;

jQuery(document).ready(function($){
    var Gals = $("#galleryArea").find("ul.gal");        
    currentlength = Gals.children('li').length;

    // sortable
    $(function() {
        $( "#sortable" ).sortable({
        connectWith: ['ul'],
        placeholder: 'placeholder',
        opacity:'0.5',
        update: function (e, ui) {
            updated_flag = 1;
            chk_updated();
        }
        });
        $( "#sortable" ).disableSelection();
    });

    var custom_uploader;
    $('.demo-media').on('click', function(e){
        parentCl = $(this).parents('li.cont').attr('id');

        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media({
            title: 'Select Image',
            // Arrow only images
            library: {
                type: 'image'
            }, 
            button: {
                text: 'Choose Image'
            },
            multiple: true // falseにすると画像を1つしか選択できなくなる
        });
        custom_uploader.on('select', function() {
            var images = custom_uploader.state().get('selection');

            images.each(function(file){
                /*
                    file.toJSON() contains image informations.
                */
                var imgFrame = $("#galleryArea").find('ul.gal').find('#'+parentCl).find('div.img');
                if( imgFrame.find("img") != null ){
                    // if image already exist
                    imgFrame.find('img').remove();
                }
                $("#galleryArea").find('ul.gal').find('#'+parentCl).find('div.img')
                        .append('<img src="'+file.toJSON().url+'" />');
                $("#galleryArea").find('ul.gal').find('#'+parentCl).find('div.img').find('input.img')
                        .val(file.toJSON().id);
            });
            updated_flag = 1;
            chk_updated();

        });
        custom_uploader.open();
    })


    
    // Add Element
    $('#galleryArea a.add').on('click', function(){
        var parentUl = $("ul#sortable");        

        currentlength++;

        var clone = parentUl.children("li:nth-child(1)").clone(true); // create clone with functions.

        clone.attr('id', 'gal_' + currentlength); // add new id
        clone.find("div.img").find('img').remove(); //remove image  
        clone.find("div.img").find('input.img').val(''); // remove value
        clone.find("input.title").val(''); // remove title value
        clone.find("div.addimg").find('ul').find('li').find("a.remove").css('display','inline'); // show remove button

        parentUl.append(clone);

        return false;
    })

    // Remove Element
    $('#galleryArea a.remove').on('click', function(){
        var target = $(this).parents('li.cont');
        target.fadeOut(300);
        var removeTarget = setTimeout(function(){
            target.remove(); 
            clearTimeout(removeTarget);
            updated_flag = 1;
            chk_updated();
     
        }, 500);


        return false;
    })

    $('input.title').change(function(){
        updated_flag = 1;
        chk_updated();
    })

});

function chk_updated(){
    if(updated_flag){
        jQuery('#notice_message').css('display', 'inline');
    }
}
