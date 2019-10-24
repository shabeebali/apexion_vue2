<?php

namespace App\Http\Resources;
use App\User;
use Illuminate\Http\Resources\Json\ResourceCollection;
class Users extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = \Auth::user();
        return [
            'data' => $this->collection,
            'meta' => [
                'edit' => $user->can('update',User::class) ? 'true': 'false',
                'delete'=> 'true',
                'create'=> $user->can('create',User::class) ? 'true': 'false',
            ],
        ];
    }
}
