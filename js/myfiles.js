let body = $('body');
let fileupload = $('#fileupload');


fileupload.on('click', (e) => {
    body.attr('data-rr-ui-modal-open', true);
    body.addClass('modal-open');
    body.css('overflow', 'hidden');

    let div1 = $("<div class='fade modal-backdrop show'></div>");

    let div2 = $(`
        <div role='dialog' aria-modal='true' class='fade modal show' tabindex='-1' style='display: block;'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='uploadContainer'>
                        <section class='container'>
                            <form enctype="multipart/form-data" method="post">
                                <input type="file" style="display: block" name='file'>
                                <input type='submit'>
                            </form>
                        </section>
                    </div>
                    <div class='modal-footer'>
                        <button id='closeUpload' type='button' class='btn btn-secondary'>Отмена</button>
                    </div>
                </div>
            </div>
        </div>
    `);

    div2.find('#closeUpload').on('click', (el) => {
        body.removeAttr('data-rr-ui-modal-open');
        body.removeClass('modal-open');
        body.removeAttr('style');

        div1.remove();
        div2.remove();
    });

    body.append(div1);
    body.append(div2);
});