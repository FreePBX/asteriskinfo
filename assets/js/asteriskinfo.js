$(document).ready(function() {
    $('.dropdown-menu-filter-status-channels-btn li a').click(function(e) {
        e.preventDefault();
        var filterStatus = $(this).data('status');
        $('#channels').bootstrapTable('filterBy', { state: filterStatus });	
    });

    $('#filter-reset-btn').on('click', function() {
        $('#channels').bootstrapTable('filterBy', {});
    });
});

function modChannelsRowStyle(row, index)
{
    let classTR = '';
    switch (row.state)
    {
        case 'online':
            classTR = 'channel-status-online';
            break;

        case 'offline':
            classTR = 'channel-status-offline';
            break;

        case 'unknown':
            classTR = 'channel-status-unknown';
            break;
        
        default:
            classTR = 'channel-status-default';
    }
    return { classes: classTR };
}

function modChannelsStatusFormatter(value, row, index)
{
    let ico   = '';
    let title = '';
    switch (value)
    {
        case 'online':
            ico = 'fa-phone-square';
            title = _('Online');
            break;

        case 'offline':
            ico = 'fa-phone-square';
            title = _('Offline');
            break;

        case 'unknown':
            ico = 'fa-phone-square';
            title = _('Unknown');
            break;
        
        default:
            ico   = 'fa-exclamation';
            title = _('Undefined');
    }

    let html = sprintf('<i class="fa %s fa-2x" title="%s"></i>', ico, sprintf(_('Status: %s'), title));
    return html;
}