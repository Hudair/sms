<div class="text-uppercase text-primary font-medium-2 mb-3">{{ __('locale.developers.voice_api') }}</div>

{!!  __('locale.description.sms_api', ['brandname' => config('app.name')])  !!}

<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup">
                                    {{ route('api.sms.send') }}
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th style="width:50%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>Authorization</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>When calling our API, send your api token with the authentication type set as <code>Bearer</code> (Example: <code>Authorization: Bearer {api_token}</code>)</td>
        </tr>

        <tr>
            <td>Accept</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>Set to <code>application/json</code></td>
        </tr>

        </tbody>
    </table>
</div>


<div class="mt-4 mb-1 font-medium-2 text-primary">Send outbound SMS</div>
<p>{{ config('app.name') }}'s Programmable SMS API enables you to programmatically send SMS messages from your web application. First, you need to create a new message object. {{ config('app.name') }} returns the created message object with each request.</p>
<p> Send your first SMS message with this example request.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{ route('api.sms.send') }}
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>recipient</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Number to send message</td>
        </tr>

        <tr>
            <td>sender_id</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>The sender of the message. This can be a telephone number (including country code) or an alphanumeric string. In case of an alphanumeric string, the maximum length is 11 characters.</td>
        </tr>

        <tr>
            <td>message</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>The body of the SMS message.</td>
        </tr>


        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X POST {{ route('api.sms.send') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd' \
     -d "recipient=31612345678" \
     -d "sender_id=YourName" \
     -d "message=This is a test message"
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "sms reports with all details",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">View an SMS</div>
<p>You can use {{ config('app.name') }}'s SMS API to retrieve information of an existing inbound or outbound SMS message.</p>
<p>You only need to supply the unique message id that was returned upon creation or receiving.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/sms/<span class="text-danger">{uid}</span>
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>uid</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>A unique random uid which is created on the {{ config('app.name') }} platform and is returned upon creation of the object.</td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X GET {{ route('api.sms.view', ['uid' => '606812e63f78b']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "sms data with all details",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">View all messages</div>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/sms/
                                </code>
                            </pre>


<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X GET {{ route('api.sms.index') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "sms reports with pagination",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>





