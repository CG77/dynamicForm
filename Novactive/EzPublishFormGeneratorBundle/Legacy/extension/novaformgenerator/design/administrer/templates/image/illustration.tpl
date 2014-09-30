
<div class="upload-image">
    <form action={concat('image/upload/' )|ezurl} method="post" name="imageFormSubmit" id="imageFormSubmit" enctype="multipart/form-data" >
        <div class="panel_wrapper">
            <div class="success"></div>
            <div class="error" id="generic_file"></div>
            <div class="panel">
                <div class="field">
                    <label id="titlelabel" for="objectName">Nom</label>
                    <input id="objectName" name="objectName" size="40" type="text" {if is_set($name)}value="{$name}"{/if} title="Nom du fichier" />
                    <span class="error" id="name"></span>
                </div>
                {if is_set($objectId)|not}
                <div class="field" id="file_uploader">
                    <label id="srclabel" for="fileName">Fichier</label>
                    <input name="fileName" type="file" id="fileName" size="40" title="Choisir un fichier" />
                    <span class="error" id="file"></span>
                </div>
                {/if}
                <div class="btn">
                    <div id="links">
                        {if is_set($name)|not}
                            <input id="btnSubmitImage" name="btnSubmitImage" type="submit" value="Enregistrer" class="button" />
                        {else}
                            <input id="btnSubmitImage" name="btnSubmitImage" type="button" value="OK" class="button upload-ok" />
                            <input id="edit_image" type="button" value="Modifier" class="button" object_image_id="{$objectId}">
                            <input type="button" value="Supprimer" class="button deleteAction">
                        {/if}

                    </div>
                    <iframe id="upload_image" name="upload_image" height="0" width="0" frameborder="0" scrolling="yes"></iframe>
                </div>
                <div id="image_uploaded">
                    {if is_set($image)}
                        <img width="200" src="/{$image}" alt=""/>
                    {/if}
                </div>
            </div>
        </div>

    </form>
</div>