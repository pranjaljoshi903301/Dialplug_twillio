<?php

namespace Corals\Modules\Messaging\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Messaging\Http\Requests\MessageRequest;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Message;
use Corals\Modules\Messaging\Models\Participation;
use Corals\User\Models\User;

use Illuminate\Support\Facades\Mail;

class MessageController extends BaseController
{
    protected $excludedRequestParams = ['files'];

    public function __construct()
    {
        $this->resource_url = config('messaging.models.message.resource_url');

        $this->title = 'Messaging::module.message.title';
        $this->title_singular = 'Messaging::module.message.title_singular';

        parent::__construct();
    }

    /**
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(MessageRequest $request)
    {
        try {

            $data = $request->all();

            $messageData = [
                'discussion_id' => $data['discussion_id'],
                'participable_type' => get_class(user()),
                'participable_id' => user()->id,
                'body' => $data['body'] ?? null,
            ];

            $message = Message::create($messageData);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $message->addMedia($file)
                        ->withCustomProperties(['root' => 'user_' . user()->hashed_id])
                        ->toMediaCollection($message->mediaCollectionName);
                }
            }

            $message->discussion->indexRecord();

            if(isSuperUser()) {
                $discussion_user = Participation::where('discussion_id', (string)$data['discussion_id'])->where('participable_id', '!=', 1)->first();
                $user_data = User::where('id', (string)$discussion_user->participable_id)->first();
                $user_email = $user_data->email;
                $user_name = $user_data->name . ' ' . $user_data->last_name;
                $domain = env('APP_URL');
                $maildata=array(
                    'domain'=>$domain,
                    'name'=>$user_name,
                    'body'=>$data['body'],
                );
                Mail::send('email.emailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email)
                {
                    $emailMessage->subject('New Message Received from Super user');
                    $emailMessage->to($user_email);
                }); 
            }

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Discussion::class, 'store');
        }

        $discussion_url = config('messaging.models.discussion.resource_url') . '/' . $message->discussion->hashed_id;

        return redirectTo($discussion_url);
    }

    public function get_message_body(Message $message) {

        try {
             $message_id = $message->id;
;            $body = $message->body;

            $message = ['message_id' => $message_id, 'body' => $body];
        } catch (\Exception $exception) {
            log_exception($exception, Message::class, 'get_message_body');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    public function destroy(MessageRequest $request, Message $message)
    {
        try {
            $message->delete();

            flash(trans('Corals::messages.success.deleted', ['item' => $this->title_singular]))->success();

        } catch (\Exception $exception) {
            log_exception($exception, Discussion::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        $discussion_url = config('messaging.models.discussion.resource_url') . '/' . $message->discussion->hashed_id;

        return redirectTo($discussion_url);
    }

}
