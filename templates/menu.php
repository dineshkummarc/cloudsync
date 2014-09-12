<div>
    <a class="btn btn-large btn-primary" style="width: 235px !important;" data-toggle="modal" href="#newfield">New <strong>Cloud</strong></a>
    <div style="margin-top: 10px; padding-top: 10px;"></div>
    <?php foreach ($clouds as $key):?>
        <div class="btn-group">
            <a class="btn btn-large btn-info" style="width: 200px !important;" href="cloud.php?snum=<?= $key["number"]?>">
                <?= $key["cloudname"] ?>
            </a>
            <a class="btn btn-large btn-info dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a onclick="$('#prinput').val('<?= $key["cloudname"]?>'); renamnum= <?=$key['number']?>;" data-toggle="modal" href="#prompt">Rename</a></li>
                <li><a onclick="$('#edel').attr('href','cloud.php?dnum=<?= $key["number"]?>')" data-toggle="modal" href="#deletefield">Delete</a></li>
            </ul>
        </div>

        <br>
    <?endforeach?>
</div>

<!--   MODALS   -->
<div id="deletefield" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="$('#edel').attr('href','#')">&times;</button>
        <h3>Delete Connector?</h3>
    </div>
    <div class="modal-body"><p>Are you sure you want to delete this connector?</p></div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#edel').attr('href','#')">Cancel</a>
        <a class="btn btn-danger" id="edel" href="">Delete</a>
    </div>
</div>

<div id="prompt" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal" onclick="$('#prinput').val(''); renamnum=null">&times;</a>
        <h3>Enter name of Connector</h3>
    </div>
    <div class="modal-body">
        <input type="text" value="" id="prinput"/>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#prinput').val(''); renamnum=null">Cancel</a>
        <a id="edial" class="btn btn-primary" onclick="window.location.href='cloud.php?rnum='+renamnum+'&nam='+$('#prinput').val()">OK</a>
    </div>
</div>

<div id="newfield" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal" onclick="$('#prinput').val('')">&times;</a>
        <h3>New Connector</h3>
    </div>
    <form id="daform" method="post" action="cloud.php">
        <div class="modal-body">
            <input type="text" placeholder="Name of Connector" name="name"/><br>
            <select name="provider" id="prov" onchange="chkprov()">
                <option value=1>WebDAV</option>
                <option value=2>Google Drive</option>
            </select>
            <div id="davy">
                <input type="text" placeholder="DAV Url(http://abc.com:80/)" name="url"/><br>
                <span style="color: dimgrey">Eg:- http[s]://dav.abc.com:[port:80|443]/</span><br>
                <input type="text" placeholder="Username/Email" name="username"/><br>
                <input type="password" placeholder="Password" name="password"/>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#prinput').val('')">Cancel</a>
            <a class="btn btn-primary" onclick="$('#daform').submit()">OK</a>
        </div>
    </form>
</div>

<script type="text/javascript">/////////////////////////////////// SCRIPTS
    function chkprov(){ // New form show/hide dav element
        var prov = document.getElementById("prov");
        var selectedValue = prov.options[prov.selectedIndex].value;
        if (selectedValue == 1){
            $("#davy").show();
        }else if(selectedValue == 2){
            $("#davy").hide();
        }
    }
</script>