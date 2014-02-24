{extends file="base/index.sidebar"}

{block name="content" append}
<h1>{translate key="filebrowser.title"}</h1>
<div id="fileBrowser">
    <ol class="breadcrumb">
        {foreach $breadcrumbs as $url => $label}
            {if $label@last}
                <li class="active">{$label}</li>
            {else}
                <li><a href="{$url}">{$label}</a></li>
            {/if}
        {/foreach}
    </ol>


    {if $browser->hasRows()}
        {$browser->addToClass('table-striped')}
        {include file="base/table" table=$browser tableForm = $tableForm}
    {else}
    <p>{translate key="filebrowser.label.directory.empty"}</p>    
    {/if}
</div>

{/block}
{foreach $app.url as $url} $url{/foreach}
{block name="sidebar"}
    {include file="base/form.prototype"}
    <div class="create">
        <h6>ACTIONS</h6>

        <a href="{$actions[0]}">{translate key="filebrowser.label.directory"}</a><br/>

        <a href="{$actions[1]}">{translate key="filebrowser.label.file"}</a><br/>
    </div>
    <div class="upload">
        <legend>Upload a file</legend>
        <form id="{$form->getId()}" class="form-horizontal" enctype="multipart/form-data" method="post" action="{$tableAction}"name="form-upload" role="form">
            <fieldset>
                {call formRows form=$form}
            <input type="submit" name="upload" class="btn" value="{translate key="filebrowser.button.upload"}">
            </fieldset>
        </form>
    </div>

    <div class="clipboard">
        <legend>{translate key="filebrowser.title.clipboard"}</legend>
        {if $clipboard}
            {$clipboard->addToClass('table-condensed')}
            {$clipboard->addToClass('table-striped')}

            {include file="base/table" table=$clipboard tableForm=$clipboardForm}
        {else}
            <p>{translate key="filebrowser.label.clipboard.empty"}</p>
        {/if}
    </div>
{/block}
{block name="scripts" append}
    <script src="{$app.url.base}/js/form.js"></script>
    <script src="{$app.url.base}/js/table.js"></script>
{/block}
