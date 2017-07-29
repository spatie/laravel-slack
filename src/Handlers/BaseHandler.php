<?php

namespace Spatie\SlashCommand\Handlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Illuminate\Contracts\Bus\Dispatcher;
use Spatie\SlashCommand\HandlesSlashCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Spatie\SlashCommand\Jobs\SlashCommandResponseJob;
use Spatie\SlashCommand\Exceptions\SlackSlashCommandException;

abstract class BaseHandler implements HandlesSlashCommand
{
    use DispatchesJobs;

    /** @var \Spatie\SlashCommand\Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function respondToSlack(string $text): Response
    {
        return Response::create($this->request)->withText($text);
    }

    protected function dispatch(SlashCommandResponseJob $job)
    {
        $job->setRequest($this->request);

        return app(Dispatcher::class)->dispatch($job);
    }

    protected function abort($response)
    {
        throw new SlackSlashCommandException($response);
    }

    public function getRequest(): Request
    {
        return $this->getRequest();
    }

    /**
     * If this function returns true, the handle method will get called.
     *
     * @param \Spatie\SlashCommand\Request $request
     *
     * @return bool
     */
    abstract public function canHandle(Request $request): bool;

    /**
     * Handle the given request. Remember that Slack expects a response
     * within three seconds after the slash command was issued. If
     * there is more time needed, dispatch a job.
     *
     * @param \Spatie\SlashCommand\Request $request
     *
     * @return \Spatie\SlashCommand\Response
     */
    abstract public function handle(Request $request): Response;
}
