{extends file="base/index.sidebar"}

{block name="content" append}
    <div class="page-header">
        <h1>{translate key="filebrowser.title.editor"} <small>File editor</small></h1>
    </div>
    {include file = "base/form.prototype"}
    <form id="{$form->getId()}" class="form-horizontal" method="post" action="{$app.url.request}" role="form">
        {call formRows form=$form}
        <div class="col-lg-offset-2 col-lg-10">
        <input type="submit" class="btn btn-default" value="{translate key="button.submit"}"/>
        <a class="btn" href="{url id="filebrowser"}">{translate key="button.cancel"}</a>
        </div>

    </form>
{/block}
{block name="scripts" append}
    <script src="{$app.url.base}/js/form.js"></script>
    <script src="{$app.url.base}/js/table.js"></script>
{/block}