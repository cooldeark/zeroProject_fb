<?php

namespace App\Presenters;

use App\Transformers\FbDataTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FbDataPresenter.
 *
 * @package namespace App\Presenters;
 */
class FbDataPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FbDataTransformer();
    }
}
