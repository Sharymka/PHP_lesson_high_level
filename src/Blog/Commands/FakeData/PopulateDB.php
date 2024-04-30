<?php

namespace Geekbrains\LevelTwo\Blog\Commands\FakeData;

use Faker\Generator;
use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use PHP\highLevel\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class PopulateDB extends Command
{
    // Внедряем генератор тестовых данных и
// репозитории пользователей и статей
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');

        $this->addOption('users-number',
                        'u',
                        InputOption::VALUE_REQUIRED,
            'How many users should be created?');
        $this->addOption('posts-number',
            'p',
            InputOption::VALUE_REQUIRED,
            'How many posts should be created?');
        $this->addOption('uuid-post',
        'c',
        InputOption::VALUE_REQUIRED,
        'Uuid');

    }
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
// Создаём десять пользователей
        $this->postsRepository->deleteAllData();
        $usersNumber = $input->getOption('users-number')?? 3;
        $postsNumber = $input->getOption('posts-number')?? 3;
            for ($i = 0; $i < $usersNumber; $i++) {
                $user = $this->createFakeUser();
                $output->writeln('User created: ' . $user->username());
                for ($j = 0; $j < $postsNumber; $j++) {
                    $post = $this->createFakePost($user);
                    $output->writeln('Post created: ' . $post->title());
                }
        }

        if($uuid = $input->getOption('uuid-post')) {
            $post = $this->postsRepository ->get(new UUID($uuid));
            $user = $this->createFakeUser();
            $this->createFakeComment($post, $user);
        }
        return Command::SUCCESS;
    }
    private function createFakeUser(): User
    {
        $user = User::createFrom(
// Генерируем имя пользователя
            $this->faker->userName,
// Генерируем пароль
            $this->faker->password,
            new Name(
// Генерируем имя
                $this->faker->firstName,
// Генерируем фамилию
                $this->faker->lastName
            )
        );
// Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
// Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
// Генерируем текст
            $this->faker->realText()
        );
// Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    public function createFakeComment(Post $post, User $user) {
        $comment = new Comment(
            UUID::random(),
            $post,
            $user,
            $this->faker->text()
        );

        $this->commentsRepository->save($comment);
    }
}