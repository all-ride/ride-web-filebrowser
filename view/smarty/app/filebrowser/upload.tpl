
{form form=$form}
<legend>{translate key="filebrowser.title.upload"}</legend>
    <div class="file clearfix{fieldHasErrors name="file"} error{/fieldHasErrors}">
        {field name="file"}
        {fieldErrors name="file"}
    </div>
    
    <div class="form-actions">
        {field name="submit" class="btn" value="filebrowser.button.upload"|translate}
    </div>
{/form}