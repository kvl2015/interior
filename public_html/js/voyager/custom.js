function addOption() {
    var matched = $("table", $('.options-block'));

    var clonedTable = $('.options-table-content-clonable').clone().removeClass('options-table-content-clonable');

    $(clonedTable).find('input.code').attr({'name': 'ocode[' +matched.length+ '][]'});
    $(clonedTable).find('input.value').attr({'name': 'ovalue[' +matched.length+ '][]'});
    $('tbody', $(clonedTable)).append($(clonedTable).find('.clonable').clone().removeClass('clonable'));

    $('.options-block').append($(clonedTable));
    $('.options-block').append('<hr>')
}

function removeOption(tbl) {
    var isDelete = confirm("Are yu sure?");

    if (isDelete) {
        $(tbl).remove();

        $("table", $('.options-block') ).each(function(key, value) {
            $(value).find('input.code').attr({'name': 'ocode[' +key+ '][]'});
            $(value).find('input.value').attr({'name': 'ovalue[' +key+ '][]'});
        });
    }
}
