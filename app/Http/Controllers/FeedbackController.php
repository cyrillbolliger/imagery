<?php

namespace App\Http\Controllers;

use App\Mail\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    /**
     * Expose the names of the feedback recipients
     *
     * @return array
     */
    public function showRecipients()
    {
        return [
            'recipients' => config('app.feedback_recipients'),
        ];
    }

    /**
     * Send feedback mail
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => [
                'required',
                'string',
                'max:20000'
            ],
        ]);

        Mail::to(config('app.admin_email'))
            ->send(new Feedback(
                $request->user(),
                $data['message'],
                $request->userAgent()
            ));

        return response(null, 204);
    }
}
