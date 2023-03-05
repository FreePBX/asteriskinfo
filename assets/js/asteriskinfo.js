var filtersTables = {}

$(document).ready(function() {
    $('.dropdown-menu-filters li a').click(function(e) {
        e.preventDefault();
        let filterKey = $(this).data('filterkey');
        let filterVal = $(this).data('filterval');
        let filterMod = $(this).data('filtermod');
        let filterTab = $(this).data('filtertab');
        filterSet(filtersTables, filterMod, filterKey, filterVal);
        $('#' + filterTab).bootstrapTable('filterBy', filterParse(filtersTables, filterMod));
    });
    $('.table-filter-clean-all-btn').on('click', function() {
        let filterMod = $(this).data('filtermod');
        let filterTab = $(this).data('filtertab');
        filterClean(filtersTables, filterMod);
        $('#' + filterTab).bootstrapTable('filterBy', {});
    });
});

function filterParse(filters, module)
{
    const result = {};
    if (filters[module])
    {
        const modFilters = filters[module];
        for (const key in modFilters)
        {
            const value = modFilters[key];
            if (typeof value === 'string' && value.trim() !== '')
            {
                result[key] = value.trim();
            }
        }
    }
    return result;
}

function filterSet(filters, module, filter, value)
{
    if (!filters[module])
    {
        filters[module] = {};
    }
    filters[module][filter] = value;
}

function filterClean(filters, module)
{
    if (filters[module])
    {
        for (const key in filters[module])
        {
            filters[module][key] = '';
        }
    }
}

function modChannelsRowStyle(row, index)
{
    let classTR = '';
    switch (row.state)
    {
        case 'online':
        case 'offline':
        case 'unknown':
            classTR = sprintf('channel-status-%s', row.state);
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

function modModulesRowStyle(row, index)
{
    let classTR = '';
    switch (row.status)
    {
        case 'Running':
            classTR = 'module-status-run';
            break;

        case 'Not Running':
            classTR = 'module-status-not-run';
            break;
        
        default:
            classTR = 'module-status-unknown';
    }
    return { classes: classTR };
}

function modModulesStatusFormatter(value, row, index)
{
    let ico   = '';
    let title = '';
    switch (value)
    {
        case 'Running':
            ico = 'fa-play';
            title = _('Running');
            break;

        case 'Not Running':
            ico = 'fa-stop';
            title = _('Not Running');
            break;

        default:
            ico = 'fa-question';
            title = _('Unknown');
            break;
    }

    let html = sprintf('<i class="fa %s fa-lg" title="%s"></i>', ico, sprintf(_('Status: %s'), title));
    return html;
}