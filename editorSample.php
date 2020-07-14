<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

<form action="" method="post">
    <table>
        <tr>
            <td>
                <textarea name="col1" id="col1" cols="30" class='editor' rows="10"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="col2" id="col2" cols="30" class='editor' rows="10"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="col3" id="col3" cols="30" class='editor' rows="10"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="col4" id="col4" cols="30" class='editor' rows="10"></textarea>
            </td>
        </tr>
    </table>
    <button> ذخیره </button>
</form>

<script src='https://cdn.ckeditor.com/ckeditor5/18.0.0/classic/ckeditor.js'></script>
<script>
    var items = document.getElementsByClassName('editor');
    // console.log(items);
    for(var i = 0; i < items.length ; i++){
        ClassicEditor
            .create(items[i], {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                    ]
                }
            })
            .catch( error => {
                console.error( error );
            } );
    }    
    
</script>