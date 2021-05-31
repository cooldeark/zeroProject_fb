<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\fb_dataRepository;
use App\Entities\FbData;
use App\Validators\FbDataValidator;

/**
 * Class FbDataRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FbDataRepositoryEloquent extends BaseRepository implements FbDataRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FbData::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FbDataValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
