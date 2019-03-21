<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-29 at 08:30:49.
 */
class JGithubPackageIssuesCommentsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the GitHub object.
	 * @since  11.4
	 */
	protected $options;

	/**
	 * @var    JGithubHttp  Mock client object.
	 * @since  11.4
	 */
	protected $client;

	/**
	 * @var    JHttpResponse  Mock response object.
	 * @since  12.3
	 */
	protected $response;

	/**
	 * @var JGithubPackageIssuesComments
	 */
	protected $object;

	/**
	 * @var    string  Sample JSON string.
	 * @since  12.3
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  12.3
	 */
	protected $errorString = '{"message": "Generic Error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->options = new JRegistry;
		$this->client = $this->getMock('JGithubHttp', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('JHttpResponse');

		$this->object = new JGithubPackageIssuesComments($this->options, $this->client);
	}

	/**
	 * @covers JGithubPackageIssuesComments::getList
	 *
	 *     GET /repos/:owner/:repo/issues/:number/comments
	 *
	 * Response
	 *
	 * Status: 200 OK
	 * Link: <https://api.github.com/resource?page=2>; rel="next",
	 * <https://api.github.com/resource?page=5>; rel="last"
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
		             ->method('get')
		             ->with('/repos/joomla/joomla-platform/issues/1/comments', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform', '1'),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 * @covers JGithubPackageIssuesComments::getRepositoryList
	 *
	 *
	 * GET /repos/:owner/:repo/issues/comments
	 *
	 * By default, Issue Comments are ordered by ascending ID.
	 * Parameters
	 *
	 * sort
	 * Optional String created or updated
	 * direction
	 * Optional String asc or desc. Ignored without sort parameter.
	 * since
	 * Optional String of a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
	 *
	 * Response
	 *
	 * Status: 200 OK
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 */
	public function testGetRepositoryList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
		             ->method('get')
		             ->with('/repos/joomla/joomla-platform/issues/comments?sort=created&direction=asc', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->getRepositoryList('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testGetRepositoryListInvalidSort()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->object->getRepositoryList('joomla', 'joomla-platform', 'invalid');
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testGetRepositoryListInvalidDirection()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->object->getRepositoryList('joomla', 'joomla-platform', 'created', 'invalid');
	}

	public function testGetRepositoryListSince()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$date = new JDate('1966-09-15 12:34:56');

		$this->client->expects($this->once())
		             ->method('get')
		             ->with('/repos/joomla/joomla-platform/issues/comments?sort=created&direction=asc&since=1966-09-15T12:34:56+00:00', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->getRepositoryList('joomla', 'joomla-platform', 'created', 'asc', $date),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 *
	 *  Method to get a single comment.
	 *
	 * GET /repos/:owner/:repo/issues/comments/:id
	 *
	 * Response
	 *
	 * Status: 200 OK
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 *
	 * {
	 * "id": 1,
	 * "url": "https://api.github.com/repos/octocat/Hello-World/issues/comments/1",
	 * "body": "Me too",
	 * "user": {
	 * "login": "octocat",
	 * "id": 1,
	 * "avatar_url": "https://github.com/images/error/octocat_happy.gif",
	 * "gravatar_id": "somehexcode",
	 * "url": "https://api.github.com/users/octocat"
	 * },
	 * "created_at": "2011-04-14T16:00:49Z",
	 * "updated_at": "2011-04-14T16:00:49Z"
	 * }
	 */
	public function testGet()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
		             ->method('get')
		             ->with('/repos/joomla/joomla-platform/issues/comments/1', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', 1),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 * @covers JGithubPackageIssuesComments::edit
	 *
	 *     PATCH /repos/:owner/:repo/issues/comments/:id
	 *
	 * Input
	 *
	 * body
	 * Required string
	 *
	 * {
	 * "body": "String"
	 * }
	 *
	 * Response
	 *
	 * Status: 200 OK
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 *
	 * {
	 * "id": 1,
	 * "url": "https://api.github.com/repos/octocat/Hello-World/issues/comments/1",
	 * "body": "Me too",
	 * "user": {
	 * "login": "octocat",
	 * "id": 1,
	 * "avatar_url": "https://github.com/images/error/octocat_happy.gif",
	 * "gravatar_id": "somehexcode",
	 * "url": "https://api.github.com/users/octocat"
	 * },
	 * "created_at": "2011-04-14T16:00:49Z",
	 * "updated_at": "2011-04-14T16:00:49Z"
	 * }
	 */
	public function testEdit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
		             ->method('patch')
		             ->with('/repos/joomla/joomla-platform/issues/comments/1', '{"body":"Hello"}', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->edit('joomla', 'joomla-platform', 1, 'Hello'),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 * @covers JGithubPackageIssuesComments::create
	 *
	 *         POST /repos/:owner/:repo/issues/:number/comments
	 *
	 * Input
	 *
	 * body
	 * Required string
	 *
	 * {
	 * "body": "a new comment"
	 * }
	 *
	 * Response
	 *
	 * Status: 201 Created
	 * Location: https://api.github.com/repos/user/repo/issues/comments/1
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 *
	 * {
	 * "id": 1,
	 * "url": "https://api.github.com/repos/octocat/Hello-World/issues/comments/1",
	 * "body": "Me too",
	 * "user": {
	 * "login": "octocat",
	 * "id": 1,
	 * "avatar_url": "https://github.com/images/error/octocat_happy.gif",
	 * "gravatar_id": "somehexcode",
	 * "url": "https://api.github.com/users/octocat"
	 * },
	 * "created_at": "2011-04-14T16:00:49Z",
	 * "updated_at": "2011-04-14T16:00:49Z"
	 * }
	 */
	public function testCreate()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
		             ->method('post')
		             ->with('/repos/joomla/joomla-platform/issues/1/comments', '{"body":"Hello"}', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', 1, 'Hello'),
			$this->equalTo(json_decode($this->response->body))
		)
		;
	}

	/**
	 * @covers JGithubPackageIssuesComments::delete
	 *
	 *     DELETE /repos/:owner/:repo/issues/comments/:id
	 *
	 * Response
	 *
	 * Status: 204 No Content
	 * X-RateLimit-Limit: 5000
	 * X-RateLimit-Remaining: 4999
	 */
	public function testDelete()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
		             ->method('delete')
		             ->with('/repos/joomla/joomla-platform/issues/comments/1', 0, 0)
		             ->will($this->returnValue($this->response))
		;

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-platform', 1, 'Hello'),
			$this->equalTo(true)
		)
		;
	}
}
