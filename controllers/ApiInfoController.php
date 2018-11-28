<?php

namespace app\controllers;

use app\models\InfoComment;
use app\models\InfoCommentVote;
use app\models\Country;
use Yii;
use \app\models\Info;



class ApiInfoController extends \app\components\ApiController {

    public function actionGetView($view) {

        $info = Info::find()->where(['view'=>$view])->one();
        $dataInfo = $info->toArray();

        $data = $info->toArray(['id']);
		
		if(Yii::$app->user->identity->country_id){
			if(!empty($dataInfo['title_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)])) {
				$data['title'] = $dataInfo['title_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)];
			} else {
				$data['title'] = $dataInfo['title_en'];
			}

			if(!empty($dataInfo['description_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)])) {
				$data['description'] = $dataInfo['description_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)];
			} else {
				$data['description'] = $dataInfo['description_en'];
			}
		} else {
            $data['title'] = $dataInfo['title_en'];
            $data['description'] = $dataInfo['description_en'];
		}


        $countryList = InfoComment::getCountryList($info->id);
        $currentCountry[] = [];
		foreach ($countryList as $itemCountry) {
		    if($itemCountry['id']==Yii::$app->user->identity->country_id) {
                $currentCountry[]=$itemCountry;
		        break;
            }
        }

        return [
            'info'=>$data,
            'infoComments'=>$this->getComments($info->id, Yii::$app->user->identity->country_id),
			'currentCountry'=>$currentCountry,
            'view'=>$view,
            'countryList'=>$countryList
        ];
    }
	
	
	private function reloadView($info_id,$country_id=null) {

        $info = Info::find()->where(['id'=>$info_id])->one();
        $dataInfo = $info->toArray();

        $data = $info->toArray(['id']);
		if($country_id==null && Yii::$app->user->identity->country_id ){
			if(!empty($dataInfo['title_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)])) {
				$data['title'] = $dataInfo['title_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)];
			} else {
				$data['title'] = $dataInfo['title_en'];
			}

			if(!empty($dataInfo['description_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)])) {
				$data['description'] = $dataInfo['description_'.Country::getCountryLanguage(Yii::$app->user->identity->country_id)];
			} else {
				$data['description'] = $dataInfo['description_en'];
			}
		}
		elseif($country_id){
			if(!empty($dataInfo['title_'.Country::getCountryLanguage($country_id)])) {
				$data['title'] = $dataInfo['title_'.Country::getCountryLanguage($country_id)];
			} else {
				$data['title'] = $dataInfo['title_en'];
			}

			if(!empty($dataInfo['description_'.Country::getCountryLanguage($country_id)])) {
				$data['description'] = $dataInfo['description_'.Country::getCountryLanguage($country_id)];
			} else {
				$data['description'] = $dataInfo['description_en'];
			}
		}
		else{	
				$data['title'] = $dataInfo['title_en'];
				$data['description'] = $dataInfo['description_en'];			
		}
        return $data;
    }


    public function actionAddComment() {
        $data = Yii::$app->request->getBodyParam('infoComment');
        $info_id = Yii::$app->request->getBodyParam('info_id');
		$country_id=Yii::$app->request->getBodyParam('country_id');

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model=new InfoComment();
        $model->user_id=Yii::$app->user->id;
        $model->info_id = $info_id;
        $model->dt=(new \app\components\EDateTime())->sqlDateTime();
        $model->lang = $country_id ? $country_id : Yii::$app->user->identity->country_id;

        $model->load($data,'');
        $model->file_id=\app\models\File::getIdFromProtected($data['file_id']);

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['infoComment'=>$data];
        }

        $trx->commit();

        return [
            'result'=>true,
            'infoComments'=>$this->getComments($info_id,$country_id,'votes_up',1),
            'countryList'=>InfoComment::getCountryList($info_id, true)
        ];
    }


    private function getComments($info_id,$country_id,$sort='votes_up',$pageNum=1) {
        $perPage = 10;
		
        $query=InfoComment::find()
            ->joinWith(['user'])
            ->where(['info_comment.info_id'=>$info_id]);

        if($country_id) {
            $query->andWhere(['info_comment.lang'=>$country_id]);
        }

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['info_comment.status'=>InfoComment::STATUS_ACTIVE]);
        }

        switch ($sort) {
            case 'dt':
                $query->orderBy(['info_comment.id'=>SORT_DESC]);
                break;
            case 'votes_up':
                $query->orderBy(['info_comment.votes_up'=>SORT_DESC]);
                break;
        }

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $infoComments=$query->all();
        $hasMore=count($infoComments)>$perPage;

        foreach(array_slice($infoComments,0,$perPage) as $item) {
            $data[]=$item->getFrontInfo();
        }
		
        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }


    public function actionListComments($info_id, $country_id=null, $sort, $pageNum) {
        return $this->getComments($info_id, $country_id, $sort, $pageNum);
    }

    public function actionVoteComment() {
        $id=Yii::$app->request->getBodyParam('id');
        $vote=Yii::$app->request->getBodyParam('vote')>0 ? 1:-1;

        $trx=Yii::$app->db->beginTransaction();

        $model=\app\models\InfoCommentVote::findOne(['info_comment_id'=>$id,'user_id'=>Yii::$app->user->id]);

        if (!$model) {
            $model=new \app\models\InfoCommentVote();
            $model->user_id=Yii::$app->user->id;
            $model->info_comment_id=$id;
            $model->vote=$vote;
            $model->dt=(new \app\components\EDateTime)->sqlDateTime();
            $model->save();

            \app\models\InfoComment::updateAllCounters(['votes_up'=>$vote>0 ? 1:0,'votes_down'=>$vote<0 ? 1:0],['id'=>$id]);
        } else {
            $votes_up=$model->vote==1 ? -1:0;
            $votes_down=$model->vote==-1 ? -1:0;
            $model->vote=$vote;
            $model->save();
            if ($model->vote>0) {
                $votes_up++;
            } else {
                $votes_down++;
            }

            \app\models\InfoComment::updateAllCounters(['votes_up'=>$votes_up,'votes_down'=>$votes_down],['id'=>$id]);
        }

        $model=\app\models\InfoComment::findOne($id);

        if ($model && $model->user_id==Yii::$app->user->id) {
            return ['result'=>Yii::t('app','Du kannst keine Stimme für Deine eigene Nachricht abgeben')];
        }

        $trx->commit();

        return ['result'=>Yii::t('app','Deine Stimme wurde abgegeben'),'comment'=>$model->getFrontInfo()];
    }



    private function getVotesComment($id,$pageNum=1) {
        $perPage = 30;
        $query=InfoCommentVote::find()
            ->with(['user'])
            ->where(['info_comment_id'=>$id]);

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $votes=$query->all();
        $hasMore=count($votes)>$perPage;

        foreach(array_slice($votes,0,$perPage) as $item) {
            $data[] = [
                'info_comment_id'=>$item->info_comment_id,
                'vote'=>$item->vote,
                'user'=>[
                    'id'=>$item->user->id,
                    'first_name'=>$item->user->first_name,
                    'last_name'=>$item->user->last_name,
                    'is_company_name'=>$item->user->is_company_name,
                    'company_name'=>$item->user->company_name,
                    'avatar'=>$item->user->getAvatarThumbUrl('avatarMobile')
                ]
            ];
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ],
            'comment_id'=>$id
        ];
    }


    public function actionVotesComment($id) {
        return $this->getVotesComment($id);
    }

    public function actionListVotesComment($id,$pageNum) {
        return $this->getVotesComment($id,$pageNum);
    }
	/*nviimedia*/
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
		
	}
	/*nviimedia*/


	public function actionAcceptComment() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::acceptInfoComment($id);
    }

    public function actionRejectComment() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::rejectInfoComment($id);
    }


}
