<div id="filters">
    <nav class="skipnav" >
        <a href="#results">Skip filters</a>
    </nav>

    <h2 class="wdn-brand">Filter Results</h2>
    <div class="filters" aria-controls="results">
        <div class="affiliation">
            <button aria-controls="filters_affiliation">By Affiliation <span class="toggle">(Expand)</span></button>
            <div class="filter-options" id="filters_affiliation" data="" role="region" tabindex="-1" aria-expanded="false" aria-live="polite"></div>
        </div>
        <div class="department">
            <button aria-controls="filters_department">By Department <span class="toggle">(Expand)</span></button>
            <div class="filter-options" id="filters_department" role="region" tabindex="-1" aria-expanded="false" aria-live="polite"></div>
        </div>
    </div>
</div>

<script id="filterOptionListTempalte" type="text/x-jsrender">
<ol>
    <li>
        <input id="filterAll{{:type}}" class="filterAll" type="checkbox" checked/>
        <label for="filterAll{{:type}}">All<span class="wdn-text-hidden"> {{:type}}</label>
    </li>
</ol>
</script>

<script id="filterOptionTemplate" type="text/x-jsrender">
<li>
    <input id="filter{{:type}}" value="{{:type}}" type="checkbox"/>
    <label for="filter{{:type}}">{{:label}}</label>
</li>
</script>

<script id="summaryTemplate" type="text/x-jsrender">
<p class="summary" area-live="polite">Displaying People: </p>
</script>

<script id="summaryAllTemplate" type="text/x-jsrender">
<span class="all selected-options">All Options</span>
</script>

<script id="summaryFilterTemplate" type="text/x-jsrender">
    <span class="{{:filterValue}} selected-options"><span class="group">{{:filterType}}</span>{{:filterLabel}}</span> <span class="operator">OR</span>

</script>
