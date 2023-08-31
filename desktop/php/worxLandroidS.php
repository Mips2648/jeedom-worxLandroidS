<?php

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('worxLandroidS');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">

    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
            <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="<?= $plugin->getDocumentation() ?>">
                <i class="fas fa-book"></i>
                <br>
                <span>{{Documentation}}</span>
            </div>
            <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="https://community.jeedom.com/tags/plugin-<?= $plugin->getId() ?>">
                <i class="fas fa-comments"></i>
                <br>
                <span>Community</span>
            </div>
            <div class="cursor logoSecondary" id="bt_syncworxLandroidS">
                <i class="fas fa-sync"></i>
                <br>
                <span>{{Synchroniser}}</span>
            </div>
            <div class="cursor logoSecondary" id="bt_activityworxLandroidS">
                <i class="fas fa-history"></i>
                <br>
                <span>{{Rapport d'activité}}</span>
            </div>
        </div>


        <legend><i class="fas fa-robot"></i> {{Mes Landroids}}</legend>
        <?php
        if (count($eqLogics) == 0) {
            echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement trouvé, vérifiez la configuration}}</div>';
        } else {
            echo '<div class="input-group" style="margin:5px;">';
            echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
            echo '<div class="input-group-btn">';
            echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
            echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
            echo '</div>';
            echo '</div>';
            echo '<div class="eqLogicThumbnailContainer">';
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '">';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '<span class="hiddenAsCard displayTableRight hidden">';
                echo '<span class="label label-info" title="{{Numéro de série}}">' . $eqLogic->getConfiguration('serial_number') . '</span>';
                echo '<span class="label label-info" title="{{Adresse MAC}}">' . $eqLogic->getConfiguration('mac_address') . '</span>';
                echo '<span class="label label-info" title="{{Version firmware}}">' . $eqLogic->getConfiguration('firmware_version') . '</span>';
                echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
                echo '</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex;">
            <span class="input-group-btn">
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
                </a>
            </span>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
            <li role="presentation"><a href="#gardentab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-home"></i> {{Ma pelouse}}</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-7">
                            <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Objet parent}}</label>
                                <div class="col-sm-6">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                        $options = '';
                                        foreach ((jeeObject::buildTree(null, false)) as $object) {
                                            $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                        }
                                        echo $options;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Catégorie}}</label>
                                <div class="col-sm-8">
                                    <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                        echo '<label class="checkbox-inline">';
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Options}}</label>
                                <div class="col-sm-6">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
                                </div>
                            </div>

                            <legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Utiliser le widget préconfiguré}}</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="automaticWidget" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> {{Durée de vie estimée des lames (Hr)}}</label>
                                <div class="col-sm-3">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="maxBladesDuration" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-3">
                                    <a id="bt_createCommands" class="btn btn-default"><i class="fas fa-search"></i> {{Créer les commandes manquantes}}</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Type Tondeuse}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="product_code"></span>
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="product_description"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Année de production}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="product_year"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Largeur de coupe}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="product_cutting_width"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Adresse MAC}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="mac_address"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Numéro de série}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="serial_number"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Date d'achat}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="purchased_at"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Date d'enregistrement}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="registered_at"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Date de fin de garantie}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="warranty_expires_at"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> {{Version firmware}}</label>
                                <div class="col-sm-3">
                                    <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="firmware_version"></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <br />
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
                            <th style="min-width:220px;width:350px;">{{Nom}}</th>
                            <th style="min-width:140px;width:160px;">{{Type}}</th>
                            <th style="min-width:260px;width:280px;">{{Options}}</th>
                            <th style="width:200px;">{{Etat}}</th>
                            <th style="min-width:80px;width:200px;">{{Actions}}</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="gardentab">
                <br />
                <div class="col-lg-6 tab-pane">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fas fa-info"></i> {{Informations}}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Surface}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="lawn_size"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Périmètre}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info eqLogicAttr" data-l1key="configuration" data-l2key="lawn_perimeter"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Délai pluie}}</label>
                                        <div class="col-sm-6">
                                            <span id="rain_delay"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 tab-pane" id="div_zonePanel">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fas fa-grip-horizontal"></i> {{Multi-Zone}}
                                <a class="btn btn-success btn-xs pull-right" id="bt_saveZone" style="top: -2px !important;"><i class="fas fa-check-circle icon-white"></i> {{Sauvegarder}}</a>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <table id="table_zone" class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">{{Zone}}</th>
                                        <th style="width:150px;">{{Distance de départ}}</th>

                                        <th>{{Répartition}}</th>
                                        <th style="width:50px;"></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // $areaListCmd         = $eqLogic->getCmd(null, 'areaList');
                                    // $areaListDistCmd     = $eqLogic->getCmd(null, 'areaListDist');
                                    // if (is_object($areaListCmd) && is_object($areaListDistCmd)) {
                                    //     $areaListCurrent     = $areaListCmd->execCmd();
                                    //     $areaListDistCurrent = $areaListDistCmd->execCmd();
                                    //     $areaList = explode('|', $areaListCurrent);
                                    //     $areaListDist = explode('|', $areaListDistCurrent);
                                    //     echo '<fieldset>';
                                    //     $count = 0;
                                    //     foreach ($areaList as $area) {

                                    //         echo '<tr><td><input id="area' . $count . '" class="form-control" type="number" name="distance" min="0" max="999" STYLE="margin:1px;" value="' . $area . '" required></td>';

                                    //         $countDist = 0;
                                    //         foreach ($areaListDist as $dist) {
                                    //             $checked = $dist == $count ? 'checked' : '';
                                    //             echo '<td><input id="dist' . $count . $countDist . '" type="radio"  name="areaDist' . $countDist . '" STYLE="margin:1px;"' .
                                    //                 ' value="distVal' . $count . $countDist . '" ' . $checked . ' >'
                                    //                 . '</td>';
                                    //             $countDist += 1;
                                    //         }
                                    //         echo '</tr>';
                                    //         echo '</fieldset>';
                                    //         $count += 1;
                                    //     }
                                    // }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 tab-pane" id="div_schedulesPanel">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="far fa-calendar-alt"></i> {{Programmation manuelle}}
                                <a class="btn btn-success btn-xs pull-right" id="bt_saveSchedules" style="top: -2px !important;"><i class="fas fa-check-circle icon-white"></i> {{Sauvegarder}}</a>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Statut}}</label>
                                        <div class="col-sm-6">
                                            <span id="schedule"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table id="table_schedules" class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">{{Jour}}</th>
                                        <th style="width: 20px;">{{Début}}</th>
                                        <th style="width: 20px;">{{Fin}}</th>
                                        <th style="width: 20px;">{{Durée}}</th>
                                        <th style="width: 20px;">{{Bord}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 tab-pane" id="div_autoschedulesPanel">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="far fa-calendar-alt"></i> {{Auto-programmation}}
                                <a class="btn btn-success btn-xs pull-right" id="bt_saveSchedules" style="top: -2px !important;"><i class="fas fa-check-circle icon-white"></i> {{Sauvegarder}}</a>
                            </h3>
                        </div>
                        <div class="panel-body">

                            <form class="form-horizontal">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Statut}}</label>
                                        <div class="col-sm-6">
                                            <span id="auto_schedule"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Espèces d'herbe}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info" id="grass_type"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Type de sol}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info" id="soil_type"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Irrigation}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info" id="irrigation"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"> {{Nutrition}}</label>
                                        <div class="col-sm-6">
                                            <span class="label label-info" id="nutrition"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table id="table_exclusions" class="table table-bordered table-condensed">
                                <caption><label><i class="far fa-clock"></i> {{Non-temps de travail}}</label></caption>
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">{{Jour}}</th>
                                        <th style="width: 20px;">{{Début}}</th>
                                        <th style="width: 20px;">{{Fin}}</th>
                                        <th style="width: 20px;">{{Durée}}</th>
                                        <th style="width: 20px;">{{Exclu}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'worxLandroidS', 'js', 'worxLandroidS'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>