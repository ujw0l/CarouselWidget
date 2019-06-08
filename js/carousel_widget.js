let carouselmMediaButton = document.querySelector('#carousel_widget_media');
var submitButton = document.querySelector('#add_carousel_image');
var deleteItemButtons = document.querySelectorAll('.carousel_delete_item');

if (null !== carouselmMediaButton) {

    carouselmMediaButton.addEventListener('click', () => {
        // Accepts an optional object hash to override default values.
        var frame = new wp.media.view.MediaFrame.Select({
            // Modal title
            title: 'Select Carousel Image',

            multiple: false,
            library: {
                order: 'ASC',
                orderby: 'title',
                type: 'image',

                priority: 20,
                toolbar: 'main-insert',
            },
            button: {
                text: 'Select Image'
            }
        })

        frame.on('close', function () {});

        frame.on('select', function () {
            var selectionCollection = frame.state().get('selection');
            var file = selectionCollection.map(function (attachment) {
                attachment = attachment.toJSON();
                document.querySelector('#image_url').value = attachment.url;
            }).join();
        });

        frame.open();
    });

}


if (null !== submitButton) {

    submitButton.addEventListener('click', (event) => {

        event.preventDefault();

        var carouselForm = document.querySelector('#carousel_add_image');

        var data = '&imageurl=' + carouselForm.querySelector("input[name='imageurl']").value;
        data += '&url=' + carouselForm.querySelector("input[name='url']").value;
        data += '&site=' + carouselForm.querySelector("input[name='site']").value;

        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", ajaxurl, true);
        xhttp.responseType = "json";
        xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');

        xhttp.onload = function () {
            if (this.status >= 200 && this.status < 400) {


                var res = this.response;


                if (null === res) {

                    alert("Error adding item.");
                }
                if (res[0] === 'true') {
                    document.querySelector('#carousel_add_image').reset();
                    alert(res[1]);
                }



            } else {
                alert(this.response);
            }
        };

        xhttp.onerror = function () {

            alert("Error adding item.");
        };
        xhttp.send("action=add_carousel_item&" + data);

    });

}

if (0 !== deleteItemButtons.length) {

    deleteItemButtons.forEach((deleteItem) => {

        deleteItem.addEventListener('click', (event) => {

            if (confirm("Are you sure, you want to delete this item?")) {
                var itemId = event.target.getAttribute('data-item-id');


                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", ajaxurl, true);
                xhttp.responseType = "json";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');

                xhttp.onload = function () {
                    if (this.status >= 200 && this.status < 400) {

                        var res = this.response;

                        if (res[0] === 'true') {
                            var rowToRemove = document.querySelector('#carousel_item_row_' + res[2]);
                            rowToRemove.outerHTML = '';
                            alert(res[1]);
                        } else {
                            alert(res[1]);
                        }


                    } else {
                        alert(this.response);
                    }
                };

                xhttp.onerror = function () {

                    alert("Error removing item.");
                };


                xhttp.send("action=delete_carousel_item&rowId=" + itemId);

            }

        });
    });

}