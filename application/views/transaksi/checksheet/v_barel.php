<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/webcamjs/webcam.js')?>"></script>
<script type="text/javascript">
    $.extend($.fn.datebox.defaults,{
        formatter:function(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:function(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    });
        
    $.extend($.fn.datetimebox.defaults,{
        formatter:function(date){
            var h = date.getHours();
            var M = date.getMinutes();
            var s = date.getSeconds();
            function formatNumber(value){
                return (value < 10 ? '0' : '') + value;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var r = $.fn.datebox.defaults.formatter(date) + ' ' + formatNumber(h)+separator+formatNumber(M);
            if ($(this).datetimebox('options').showSeconds){
                r += separator+formatNumber(s);
            }
            return r;
        },
        parser:function(s){
            if ($.trim(s) == ''){
                return new Date();
            }
            var dt = s.split(' ');
            var d = $.fn.datebox.defaults.parser(dt[0]);
            if (dt.length < 2){
                return d;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var tt = dt[1].split(separator);
            var hour = parseInt(tt[0], 10) || 0;
            var minute = parseInt(tt[1], 10) || 0;
            var second = parseInt(tt[2], 10) || 0;
            return new Date(d.getFullYear(), d.getMonth(), d.getDate(), hour, minute, second);
        }
    });
</script>
<!-- Data Grid -->
<table id="grid-transaksi_barel"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_barel">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'barel_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'barel_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'barel_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'barel_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Barel</th>
            <th data-options="field:'barel_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_barel = [{
        text:'New',
        id:'baruBarel',
        iconCls:'icon-new_file',
        handler:function(){transaksiBarelCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiBarelUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiBarelHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiBarelSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiBarelImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_barel').datagrid('reload');}
    },{
        text:'Update Tgl. Barel After',
        iconCls:'icon-date',
        handler:function(){transaksiBarelAfter();}
    },{
        text:'Update Tgl. Barel Between',
        iconCls:'icon-date',
        handler:function(){transaksiBarelBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiBarelCheck();}
    }];
    
    $('#grid-transaksi_barel').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/barel/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruBarel').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiBarelCreate() {
        $('#dlg-transaksi_barel').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_barel').form('clear');
        //$('#barel_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/barel/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/barel/getDateBarel'); ?>',function(result){
            $('#barel_date').datebox('setValue', result.sesdate);
            },'json'); //ambil session date

        Webcam.set({
            width: 430,
            height: 240,
            dest_width: 1280,
            dest_height: 720,
            force_flash: true,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.setSWFLocation('assets/webcamjs/webcam.swf');
        Webcam.attach('#webcamBarel');
        
        $('#captureBarel').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureBarel').focus();
        $('#captureBarel').bind('click', function(){
            $('#captureBarel').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#barel_lot').textbox('setValue', '');
            $('#barel_customer').textbox('setValue', '');
            $('#barel_barang').textbox('setValue', '');
            $('#barel_sub').textbox('setValue', '');
            $('#barel_lot').next().find('input').focus();
        });
       
        $('#barel_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotBarel').focus();
            }
        });
        
        $('#chlotBarel').bind('click', function(){
            $('#barel_sub').next().find('input').focus();
        });
        
        $('#barel_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#barel_oksave').focus();
            }
        });
    }
    
    function captureBarel() {
        Webcam.snap( function(data_uri) {
            $('#imgBarel').textbox('setValue',data_uri);
        });
    }
    
    function checkLotBarel() {
        var lotidBarel = $('#barel_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/barel/cekLot'); ?>',{barel_lot:lotidBarel},function(result){
            if (result.success){
                $('#barel_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/barel/getCustItem'); ?>',{barel_lot:lotidBarel},function(result){
                    $('#barel_customer').textbox('setValue', result.customer);
                    $('#barel_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#barel_lot').textbox('setValue', '');
                $('#barel_customer').textbox('setValue', '');
                $('#barel_barang').textbox('setValue', '');
                $('#barel_sub').textbox('setValue', '');
                $('#barel_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiBarelUpdate() {
        var row = $('#grid-transaksi_barel').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_barel-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_barel-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/barel/update'); ?>/' + row.barel_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiBarelSave(){
        $('#fm-transaksi_barel').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_barel').dialog('close');
                    //$('#grid-transaksi_barel').datagrid('reload');
                    transaksiBarelCreate();
                    $.messager.show({
                        title: 'Info',
                        msg: 'Input Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Input Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBarelSaveEdit(){
        $('#fm-transaksi_barel-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_barel-edit').dialog('close');
                    $('#grid-transaksi_barel').datagrid('reload');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBarelHapus(){
        var row = $('#grid-transaksi_barel').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Barel '+row.barel_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/barel/delete'); ?>',{barel_id:row.barel_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_barel').datagrid('reload');
                            $.messager.show({
                                title: 'Info',
                                msg: 'Hapus Data Berhasil'
                            });
                        } else {
                            $.messager.show({
                                title: 'Error',
                                msg: 'Hapus Data Gagal'
                            });
                        }
                    },'json');
                }
            });
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiBarelImage(){
        var row = $('#grid-transaksi_barel').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/barel/viewImage'); ?>',{barel_id:row.barel_id},function(result){
                if (result.success){
                    var content = '<iframe scbarel="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.barel_id;
                    if ($('#tt').tabs('exists', title))
                    {
                        $('#tt').tabs('select', title);
                        $('#dlg').dialog('close');
                    } 
                    else 
                    {
                        $('#tt').tabs('add',{
                            title:title,
                            content:content,
                            closable:true,
                            iconCls:'icon-picture'
                        });
                        $('#dlg').dialog('close');
                    }
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Gambar Tidak Ditemukan'
                    });
                }
            },'json');
            
        }
        else
        {
            $.messager.alert('Info','Data belum dipilih !','info');
        }
    }

    function transaksiBarelSesdate()
    {
        $('#dlg-transaksi_barel_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_barel_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/barel/getDateBarel'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiBarelSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/barel/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_barel_sesdate').dialog('close');
                $.messager.show({
                    title: 'Info',
                    msg: 'Ubah Data Berhasil'
                });
            }
            else
            {
                $.messager.show({
                    title: 'Error',
                    msg: 'Ubah Data Gagal'
                });
            }
        },'json');
    }
    
    function transaksiBarelAfter()
    {
        $('#dlg-transaksi_barel-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Barel After');
        $('#fm-transaksi_barel-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/barel/updateAfter'); ?>';
    }
    
    function transaksiBarelAfterSave()
    {
        $('#fm-transaksi_barel-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_barel').datagrid('reload');
                    $('#dlg-transaksi_barel-after').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBarelBetween()
    {
        $('#dlg-transaksi_barel-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Barel Between');
        $('#fm-transaksi_barel-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/barel/updateBetween'); ?>';
    }
    
    function transaksiBarelBetweenSave()
    {
        $('#fm-transaksi_barel-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_barel').datagrid('reload');
                    $('#dlg-transaksi_barel-between').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBarelCheck()
    {
        $('#dlg-transaksi_barel-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_barel-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/barel/check'); ?>';
    }
    
    function transaksiBarelCheckSave()
    {
        $('#fm-transaksi_barel-check').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    $.messager.show({
                        title: 'Info',
                        msg: 'Total Entry Data Tanggal '+result.tgl+' Sebanyak '+result.total+' Data'
                    }); 
                }                               
            }
        });
    }
    
</script>
<style type="text/css">
    #fm-transaksi_barel{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_barel-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_barel-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_barel-between{
        margin:0;
        padding:10px 30px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
    .fitem input{
        display:inline-block;
        width:150px;
    }
    .center a{
        margin-left: 200px;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .grup2{
        margin-left: 75px;
    }

</style>

<div id="dlg-transaksi_barel" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel">
    <form id="fm-transaksi_barel" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamBarel">
            </div>
        </div>
        <div class="center">
            <a id="captureBarel" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureBarel()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgBarel" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="barel_lot" name="barel_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotBarel" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotBarel()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="barel_sub" name="barel_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="barel_customer" name="barel_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="barel_barang" name="barel_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Barel</label>
                <input id="barel_date" name="barel_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel">
    <a href="javascript:void(0)" id="barel_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiBarelSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel').dialog('close');Webcam.reset();$('#grid-transaksi_barel').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_barel-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel-edit">
    <form id="fm-transaksi_barel-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="barel_lot" name="barel_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="barel_sub" name="barel_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Barel</label>
            <input type="text" id="barel_date" name="barel_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBarelSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_barel_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel_sesdate">
    <form id="fm-transaksi_barel_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBarelSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_barel-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel-after">
    <form id="fm-transaksi_barel-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Barel</label>
            <input type="text" id="aa" name="aa" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="bb" name="bb" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="cc" name="cc" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBarelAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_barel-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel-between">
    <form id="fm-transaksi_barel-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Barel</label>
            <input type="text" id="dd" name="dd" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="ee" name="ee" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sebelum Input</label>
            <input type="text" id="ff" name="ff" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="gg" name="gg" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBarelBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_barel-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_barel-check">
    <form id="fm-transaksi_barel-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Barel</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_barel-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBarelCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_barel-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_barel.php -->
<!-- Location: ./application/views/transaksi/v_barel.php -->