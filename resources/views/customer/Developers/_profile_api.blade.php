<div class="text-uppercase text-primary font-medium-2 mb-3">{{ __('locale.labels.profile') }} {{ __('locale.labels.api') }}</div>

{!!  __('locale.description.profile_api', ['brandname' => config('app.name')])  !!}

<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup">
                                    {{ route('api.profile.me') }}
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


<div class="mt-4 mb-1 font-medium-2 text-primary">View sms unit</div>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{ route('api.profile.balance') }}
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X GET {{ route('api.profile.balance') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "sms unit with all details",
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


<div class="mt-4 mb-1 font-medium-2 text-primary">View Profile</div>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{ route('api.profile.me') }}
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X GET {{ route('api.profile.me') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "profile data with all details",
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


