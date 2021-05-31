<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\FbData;

/**
 * Class FbDataTransformer.
 *
 * @package namespace App\Transformers;
 */
class FbDataTransformer extends TransformerAbstract
{
    /**
     * Transform the FbData entity.
     *
     * @param \App\Entities\FbData $model
     *
     * @return array
     */
    public function transform(FbData $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
