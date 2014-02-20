<div class="clipboard">
    <legend>{translate key="filebrowser.title.clipboard"}</legend>
{if $clipboard}
    {$clipboard->addToClass('table-condensed')}
    {$clipboard->addToClass('table-striped')}
    {$clipboard->getForm()->addToClass('table-condensed')}
    {$clipboard->getForm()->addToClass('no-pagination')}
    
    {include file="app/table" table=$clipboard}
{else}
    <p>{translate key="filebrowser.label.clipboard.empty"}</p>
{/if}
</div>