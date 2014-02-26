## Set The Root Directory

The root of the default file browser is _public/upload_.
You can customize this by setting the _filebrowser.path.default_ parameter.

## Create Your Own File Browser

You can create as much file browsers as you want.

To do so, create a dependency for [ride\app\controller\FileBrowserController](/api/class/ride/app/controller/FileBrowserController) like in the following sample:

    <?xml version="1.0" encoding="UTF-8"?>
    <container>
        <dependency class="ride\library\filesystem\File" id="my.filebrowser">
            <call method="__construct">
                <argument name="path" type="parameter">
                    <property name="key" value="filebrowser.path.my" />
                </argument>
            </call>
        </dependency>
        
        <dependency class="ride\app\controller\FileBrowserController" id="my.filebrowser">
            <call method="setRoot">
                <argument name="root" type="dependency">
                    <property name="interface" value="ride\library\filesystem\File" />
                    <property name="id" value="my.filebrowser" />
                </argument>
            </call>
            <call method="setRoutePrefix">
                <argument name="prefix" type="scalar">
                    <property name="value" value="my.filebrowser." />
                </argument>
            </call>
        </dependency>
    </container>

The root directory is set through the parameter _filebrowser.path.my_, as defined in the dependency.
    
Now create the routes for your own file browser:

    <?xml version="1.0" encoding="UTF-8"?>
    <routes>
        <route path="/my.filebrowser/path" 
               controller="ride\app\controller\FileBrowserController#my.filebrowser"
               action="pathAction" 
               methods="head,get,post" 
               dynamic="true" 
               id="my.filebrowser.path" />
        <route path="/my.filebrowser/download" 
               controller="ride\app\controller\FileBrowserController#my.filebrowser"
               action="downloadAction" 
               methods="head,get" 
               dynamic="true" 
               id="my.filebrowser.download" />
        <route path="/my.filebrowser/create" 
               controller="ride\app\controller\FileBrowserController#my.filebrowser"
               action="createAction" 
               methods="head,get,post" 
               dynamic="true" 
               id="my.filebrowser.create" />
        <route path="/my.filebrowser/edit" 
               controller="ride\app\controller\FileBrowserController#my.filebrowser"
               action="editAction" 
               methods="head,get,post" 
               dynamic="true" 
               id="my.filebrowser.edit" />
        <route path="/my.filebrowser/rename" 
               controller="ride\app\controller\FileBrowserController#my.filebrowser"
               action="renameAction" 
               methods="head,get,post" 
               dynamic="true" 
               id="my.filebrowser.rename" />
    </routes>
    
## Customize The Default File Browser

The default instance of the file browser is defined as a dependency with id _filebrowser_.
You can use the [Dependencies](/manual/page/Core/Dependencies) to customize the properties of it.      

    <?xml version="1.0" encoding="UTF-8"?>
    <container>
        <dependency class="ride\library\filesystem\File" id="my.filebrowser">
            <call method="__construct">
                <argument name="path" type="parameter">
                    <property name="key" value="filebrowser.path.my" />
                </argument>
            </call>
        </dependency>
        
        <dependency class="ride\app\controller\FileBrowserController" extends="filebrowser" id="filebrowser">
            <call method="setRoot">
                <argument name="root" type="dependency">
                    <property name="interface" value="ride\library\filesystem\File" />
                    <property name="id" value="my.filebrowser" />
                </argument>
            </call>
        </dependency>
    </container>
