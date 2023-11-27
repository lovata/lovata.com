# Content Menu

Displays a context menu on right click.

```js
var $contextMenu = $(document.body).contextMenu()

$(document).on('contextmenu', '.some-container', function(ev) {
    $('body').contextMenu('openMenu', {
        pageX: ev.pageX,
        pageY: ev.pageY,
        items: menuItems
        items: [
            {
                name: 'Name',
                label: 'Label'
            },
            {
                name: 'Edit Content',
                action: () => openIframe('/some/iframe/edit')
            },
            {
                name: 'Edit Settings',
                action: () => openIframe('/some/iframe/settings')
            }
        ]
    });
});

// Close the menu
$contextMenu.contextMenu('closeMenu')
```
