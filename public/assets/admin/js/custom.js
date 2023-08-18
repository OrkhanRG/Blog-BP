function imageCheck(images)
{
    let $images = images[0];
    let length = $images.files.length;
    let files = $images.files;
    let mimeType = ['png', 'jpg', 'jpeg'];

    if (length > 0)
    {
        for (let i=0; i<length; i++)
        {
            let type = files[i].type.split('/').pop();
            let size = files[i].size;

            if ($.inArray(type, mimeType) == '-1')
            {
                Swal.fire({
                    title: 'Xəta!',
                    confirmButtonText: 'yaxşı',
                    text: 'Seçilən ' + files[i].name + ' şəkli axtarılan formatda deyil. .png, .jpg, .jpeg formtalarından biri olmalıdır.',
                    icon: 'warning',
                });
                return false;
            }
            else if (size > 2048000)
            {
                Swal.fire({
                    title: 'Xəta!',
                    confirmButtonText: 'yaxşı',
                    text: 'Seçilən' + files[i].name + 'şəklinin həcmi 2mb-dan çox ola bilməz.',
                    icon: 'warning',
                });
                return false;
            }

            return true;
        }
    }

    return true;
}

$(document).ready(function (){
    $('.languageDropDown').click(function (){
        $(this).addClass('show');
    });

    $(".btnClearFilter").click(function (){
        let filters1 = $('#formFilter input')
        let filters2 = $('#formFilter select');
        let filters = filters1.toArray().concat(filters2.toArray());
        console.log(filters2.val('').trigger('change'));

        filters.forEach(function (element, index, arr){
            element.value=null;
            if (element.nodeName == 'SLECET') {
                element.val(null).trigger('change');
            }
        });
    });

});
