<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-user']) .' Create Users', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-import']) .' Import Users', ['ajax-import'], ["id" => "importUser", 'class' => 'btn btn-success']) ?>
        <?= Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-save-file']) .'Export Users PDF', ['generar-pdf'], ['class' => 'btn btn-success', 'target' => '_blank']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>    
    <?= GridView::widget([
        'id' => 'gridUsers',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'username',
            'email:email',
            'address:ntext',
            'phone',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>

</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
            $("#importUser").click(function(e){
                e.preventDefault();
                $.ajax({
                // url: 'users/ajax-import',
                url: 'index.php?r=users/ajax-import',
                type: 'post',
                dataType: 'json',
                success: function(respJson) {
                    console.log(respJson);
                    alert(respJson.message);
                    if(respJson.status = 'success'){
                        // $.pjax({container: '#pjax-grid-view'});
                        $.pjax.reload({container:'#pjax-grid-view'});
                        // $("#pjax-grid-view").yiiGridView("applyFilter");
                    }
                },
                error: function() {
                    alert("No se ha podido obtener la información. Consulte al administrador.");
                }
            });
            });
    });
</script>