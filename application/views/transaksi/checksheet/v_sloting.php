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
<table id="grid-transaksi_sloting"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_sloting">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'sloting_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'sloting_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'sloting_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'sloting_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Sloting</th>
            <th data-options="field:'sloting_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_sloting = [{
        text:'New',
        id:'baruSloting',
        iconCls:'icon-new_file',
        handler:function(){transaksiSlotingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiSlotingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiSlotingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiSlotingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiSlotingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_sloting').datagrid('reload');}
    },{
        text:'Update Tgl. Sloting After',
        iconCls:'icon-date',
        handler:function(){transaksiSlotingAfter();}
    },{
        text:'Update Tgl. Sloting Between',
        iconCls:'icon-date',
        handler:function(){transaksiSlotingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiSlotingCheck();}
    }];
    
    $('#grid-transaksi_sloting').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/sloting/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruSloting').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiSlotingCreate() {
        $('#dlg-transaksi_sloting').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_sloting').form('clear');
        //$('#sloting_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/sloting/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/sloting/getDateSloting'); ?>',function(result){
            $('#sloting_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamSloting');
        
        $('#captureSloting').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureSloting').focus();
        $('#captureSloting').bind('click', function(){
            $('#captureSloting').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#sloting_lot').textbox('setValue', '');
            $('#sloting_customer').textbox('setValue', '');
            $('#sloting_barang').textbox('setValue', '');
            $('#sloting_sub').textbox('setValue', '');
            $('#sloting_lot').next().find('input').focus();
        });
       
        $('#sloting_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotSloting').focus();
            }
        });
        
        $('#chlotSloting').bind('click', function(){
            $('#sloting_sub').next().find('input').focus();
        });
        
        $('#sloting_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#sloting_oksave').focus();
            }
        });
    }
    
    function captureSloting() {
        Webcam.snap( function(data_uri) {
            $('#imgSloting').textbox('setValue',data_uri);
        });
    }
    
    function checkLotSloting() {
        var lotidSloting = $('#sloting_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/sloting/cekLot'); ?>',{sloting_lot:lotidSloting},function(result){
            if (result.success){
                $('#sloting_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/sloting/getCustItem'); ?>',{sloting_lot:lotidSloting},function(result){
                    $('#sloting_customer').textbox('setValue', result.customer);
                    $('#sloting_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#sloting_lot').textbox('setValue', '');
                $('#sloting_customer').textbox('setValue', '');
                $('#sloting_barang').textbox('setValue', '');
                $('#sloting_sub').textbox('setValue', '');
                $('#sloting_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiSlotingUpdate() {
        var row = $('#grid-transaksi_sloting').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_sloting-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_sloting-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/sloting/update'); ?>/' + row.sloting_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiSlotingSave(){
        $('#fm-transaksi_sloting').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_sloting').dialog('close');
                    //$('#grid-transaksi_sloting').datagrid('reload');
                    transaksiSlotingCreate();
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
    
    function transaksiSlotingSaveEdit(){
        $('#fm-transaksi_sloting-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_sloting-edit').dialog('close');
                    $('#grid-transaksi_sloting').datagrid('reload');
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
    
    function transaksiSlotingHapus(){
        var row = $('#grid-transaksi_sloting').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Sloting '+row.sloting_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/sloting/delete'); ?>',{sloting_id:row.sloting_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_sloting').datagrid('reload');
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
    
    function transaksiSlotingImage(){
        var row = $('#grid-transaksi_sloting').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/sloting/viewImage'); ?>',{sloting_id:row.sloting_id},function(result){
                if (result.success){
                    var content = '<iframe scsloting="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.sloting_id;
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

    function transaksiSlotingSesdate()
    {
        $('#dlg-transaksi_sloting_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_sloting_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/sloting/getDateSloting'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiSlotingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/sloting/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_sloting_sesdate').dialog('close');
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
    
    function transaksiSlotingAfter()
    {
        $('#dlg-transaksi_sloting-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Sloting After');
        $('#fm-transaksi_sloting-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/sloting/updateAfter'); ?>';
    }
    
    function transaksiSlotingAfterSave()
    {
        $('#fm-transaksi_sloting-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_sloting').datagrid('reload');
                    $('#dlg-transaksi_sloting-after').dialog('close');
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
    
    function transaksiSlotingBetween()
    {
        $('#dlg-transaksi_sloting-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Sloting Between');
        $('#fm-transaksi_sloting-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/sloting/updateBetween'); ?>';
    }
    
    function transaksiSlotingBetweenSave()
    {
        $('#fm-transaksi_sloting-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_sloting').datagrid('reload');
                    $('#dlg-transaksi_sloting-between').dialog('close');
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
    
    function transaksiSlotingCheck()
    {
        $('#dlg-transaksi_sloting-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_sloting-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/sloting/check'); ?>';
    }
    
    function transaksiSlotingCheckSave()
    {
        $('#fm-transaksi_sloting-check').form('submit',{
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
    #fm-transaksi_sloting{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_sloting-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_sloting-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_sloting-between{
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

<div id="dlg-transaksi_sloting" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting">
    <form id="fm-transaksi_sloting" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamSloting">
            </div>
        </div>
        <div class="center">
            <a id="captureSloting" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureSloting()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgSloting" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="sloting_lot" name="sloting_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotSloting" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotSloting()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="sloting_sub" name="sloting_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="sloting_customer" name="sloting_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="sloting_barang" name="sloting_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Sloting</label>
                <input id="sloting_date" name="sloting_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_sloting">
    <a href="javascript:void(0)" id="sloting_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiSlotingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting').dialog('close');Webcam.reset();$('#grid-transaksi_sloting').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_sloting-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting-edit">
    <form id="fm-transaksi_sloting-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="sloting_lot" name="sloting_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="sloting_sub" name="sloting_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Sloting</label>
            <input type="text" id="sloting_date" name="sloting_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_sloting-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiSlotingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_sloting_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting_sesdate">
    <form id="fm-transaksi_sloting_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_sloting_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiSlotingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_sloting-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting-after">
    <form id="fm-transaksi_sloting-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Sloting</label>
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
<div id="dlg-buttons-transaksi_sloting-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiSlotingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_sloting-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting-between">
    <form id="fm-transaksi_sloting-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Sloting</label>
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
<div id="dlg-buttons-transaksi_sloting-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiSlotingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_sloting-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_sloting-check">
    <form id="fm-transaksi_sloting-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Sloting</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_sloting-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiSlotingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_sloting-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_sloting.php -->
<!-- Location: ./application/views/transaksi/v_sloting.php -->