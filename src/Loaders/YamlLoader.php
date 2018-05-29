<?php

/*
 * This file is part of the Certificationy library.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 * (c) Mickaël Andrieu <andrieu.travail@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Certificationy\Loaders;

use Certificationy\Interfaces\LoaderInterface;
use Certificationy\Collections\Questions;
use Certificationy\Collections\Answers;
use Certificationy\Answer;
use Certificationy\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlLoader implements LoaderInterface
{
    use VersionParserAware;

    /**
     * @var Questions
     */
    private $questions;

    /**
     * @var string
     */
    private $paths;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    public function load(int $nbQuestions, array $categories, array $versions = null) : Questions
    {
        $data = $this->prepareFromYaml($categories, $versions);
        $dataMax = count($data);
        $nbQuestions = min($nbQuestions, $dataMax);

        $questions = new Questions();

        for ($i = 0; $i < $nbQuestions; $i++) {
            $key = array_rand($data);
            $item = $data[$key];
            unset($data[$key]);

            $answers = new Answers();

            foreach ($item['answers'] as $dataAnswer) {
                $answers->addAnswer(new Answer($dataAnswer['value'], $dataAnswer['correct']));
            }

            if (!isset($item['shuffle']) || true === $item['shuffle']) {
                $answers->shuffle();
            }

            $help = isset($item['help']) ? $item['help'] : null;

            $questions->add($key, new Question($item['question'], $item['category'], $answers, $help));
        }

        return $questions;
    }

    /**
     * @inheritdoc
     *
     * @throws \ErrorException
     */
    public function all() : Questions
    {
        if (is_null($this->questions)) {
            $this->questions = $this->load(PHP_INT_MAX, []);
        }

        return $this->questions;
    }

    /**
     * @inheritdoc
     */
    public function categories() : array
    {
        $categories = [];
        $files = $this->prepareFromYaml([]);

        foreach ($files as $file) {
            $categories[] = $file['category'];
        }

        return array_unique($categories);
    }

    /**
     * Prepares data from Yaml files and returns an array of questions
     *
     * @param array $categories : List of categories which should be included, empty array = all
     * @param array $versions : List of configured versions, null when versions should be ignored
     *
     * @return array
     */
    protected function prepareFromYaml(array $categories = [], array $versions = null) : array
    {
        $data = array();

        foreach ($this->paths as $path) {
            $files = Finder::create()->files()->in($path)->name('*.yml');

            foreach ($files as $file) {
                $fileData = Yaml::parse($file->getContents());
                $category = $fileData['category'];

                if (count($categories) > 0 && !in_array($category, $categories)) {
                    continue;
                }

                $fileData['questions'] = array_filter($fileData['questions'], function ($item) use ($versions) {
                    if (is_array($versions) && isset($item['versions']) && !$this->versionApplies($item['versions'], $versions)) {
                        return false;
                    }

                    return true;
                });

                array_walk($fileData['questions'], function (&$item) use ($category) {
                    $item['category'] = $category;
                });

                $data = array_merge($data, $fileData['questions']);
            }
        }

        return $data;
    }
}
