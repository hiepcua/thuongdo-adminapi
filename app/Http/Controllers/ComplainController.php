<?php

namespace App\Http\Controllers;

use App\Http\Requests\Complain\ComplainModifiesRequest;
use App\Http\Resources\Complain\ComplainCommentResource;
use App\Http\Resources\Complain\ComplainDetailResource;
use App\Http\Resources\Complain\ListCommentResource;
use App\Http\Resources\ComplainResource;
use App\Models\Complain;
use App\Models\ComplainFeedback;
use App\Models\Staff;
use App\Services\ComplainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ComplainController extends Controller
{
    public function __construct(ComplainService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  ComplainModifiesRequest  $request
     * @param  Complain  $complain
     * @return mixed
     */
    public function modifies(ComplainModifiesRequest $request, Complain $complain)
    {
        return DB::transaction(
            function () use ($complain, $request) {
                $complain->update($request->all());
                return resSuccessWithinData(new ComplainResource($complain));
            }
        );
    }

    public function detail(string $id): JsonResponse
    {
        return resSuccessWithinData(new ComplainDetailResource(Complain::query()->find($id)));
    }

    public function getFeedback(string $id, string $type): JsonResponse
    {
        return $this->getFeedbackByIdAndType($id, $type);
    }

    /**
     * @param  string  $id
     * @param  string  $type
     * @return JsonResponse
     */
    private function getFeedbackByIdAndType(string $id, string $type): JsonResponse
    {
        return resSuccessWithinData(
            new ListCommentResource(
                ComplainFeedback::query()->where(['complain_id' => $id, 'type' => $type])->get(),
                $type,
                ComplainCommentResource::class
            )
        );
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function storeFeedback(string $id)
    {
        $comment = ComplainFeedback::query()->create(
            [
                'complain_id' => $id,
                'cause_id' => getCurrentUserId(),
                'cause_type' => Staff::class,
                'content' => request()->input('content'),
                'type' => request()->input('type'),
            ]
        );
        return resSuccessWithinData($comment);
    }

    /**
     * @param  string  $id
     * @param  string  $type
     * @return JsonResponse
     */
    public function seen(string $id, string $type): JsonResponse
    {
        ComplainFeedback::query()->where(['complain_id' => $id, 'type' => $type])->update(['is_seen' => true]);
        return resSuccess();
    }
}
