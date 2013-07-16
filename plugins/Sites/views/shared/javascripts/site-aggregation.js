
var SiteAggregation = {
    addSiteKey: function() {
        var inputBlockDiv = document.createElement('div');
        inputBlockDiv.setAttribute('class', 'input-block');
        var label = document.createElement('label');
        label.appendChild(document.createTextNode('New site key'));
        var inputEl = document.createElement('input');
        inputEl.setAttribute('name', 'site_key[]');
        inputEl.setAttribute('class', 'site_key');
        inputEl.setAttribute('type', 'text');
        inputBlockDiv.appendChild(label);
        inputBlockDiv.appendChild(inputEl);
        jQuery("fieldset[name='site_keys']").append(inputBlockDiv);
    }        
}

jQuery(document).ready(function() {
    jQuery('p#add_site_key').click(SiteAggregation.addSiteKey);
});