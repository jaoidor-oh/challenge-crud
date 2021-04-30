<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use kartik\mpdf\Pdf;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $session = Yii::$app->session;
        $session->open();
        $session['query_params'] = json_encode(Yii::$app->request->queryParams);
        $session->close();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGenerarPdf() 
    {
        $session = Yii::$app->session;
        $session->open();
        $queryParams = isset($session['query_params']) ? json_decode($session['query_params'], true) : [];
        $session->close();
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchPdf($queryParams);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $formatter = \Yii::$app->formatter;
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, // leaner size using standard fonts
            'defaultFontSize' => 18,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            //Se renderiza la vista "pdf" (que abrirá la nueva ventana)
            'content' => $this->renderPartial('_expotUserPdf', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]),
            'options' => [
            // any mpdf options you wish to set
            ],
            'methods' => [
                'SetTitle' => 'Crimson Circle: Challenge Users',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Crimson Circle: Challenge Users||Generado el: ' . $formatter->asDate(date("r"))],
                'SetFooter' => ['|Página {PAGENO}|'],
                'SetAuthor' => 'Crimson Circle: Challenge Users',
                'SetCreator' => 'José Antonio Oidor Hernández',
//              'SetKeywords' => 'Sie, Yii2, Export, PDF, MPDF, Output, yii2-mpdf',
            ]
        ]);
        return $pdf->render();
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }else{
                $errors = $model->errors;
                var_dump($errors);
            }
        }
        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    function actionAjaxImport(){
        $dataInsert = 0;
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = json_decode( file_get_contents('https://jsonplaceholder.typicode.com/users'), true );

            $modelUser = new Users();
            if(!empty($data)){
                foreach ($data as $key => $items) {
                    $modelUser = Users::find()->where(['name' => $items['name'],'username' => $items['username'] ])->one();
                    if($modelUser == null){
                        $modelUser = new Users();
                        $modelUser->name = $items['name'];
                        $modelUser->username = $items['username'];
                    }
                    $modelUser->email = $items['email'];
                    $modelUser->address = $items['address']['street']."\n".$items['address']['suite']."\n".$items['address']['city']."\n".$items['address']['zipcode'];
                    $modelUser->phone = $items['phone'];
                    if($modelUser->validate()){
                        if($modelUser->save()){
                            $dataInsert++;
                        }   
                    }
                }
            }
        }
        if ($dataInsert > 0){
            return [
                'status' => "success",
                'dataInsert' => $dataInsert,
                'message' => Yii::t('app', 'Registros importados {n}', ['n'=>$dataInsert]),
            ];
        }else{
            return [
                'status' => "error",
                'message' => Yii::t('app', 'No se importo dato'),
            ];
        }
    }
}
