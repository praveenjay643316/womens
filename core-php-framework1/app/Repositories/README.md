# app/Repositories

Reserved for a repository layer if you want to decouple controllers/models
from raw query logic, e.g. `UserRepository::findActive()`. Not required for
the base framework — `App\Core\Model` already provides basic CRUD — but
useful once queries get more complex than the base Model helpers cover.
