# Balloon selector

    <div data-control="balloon-selector" class="control-balloon-selector">
        <ul>
            <li data-value="1" class="active">One</li>
            <li data-value="2">Two</li>
            <li data-value="3">Three</li>
        </ul>

        <input type="hidden" name="balloonValue" value="1" />
    </div>

If you don't define `data-control="balloon-selector"` then the control will act as a static list of labels.

    <div class="control-balloon-selector">
        <ul>
            <li>Monday</li>
            <li>Tuesday</li>
            <li>Happy days!</li>
        </ul>
    </div>
