import BuildingController from './BuildingController'
import FeedbackController from './FeedbackController'
import NeedController from './NeedController'
import AdminController from './AdminController'
import UserController from './UserController'

const Controllers = {
    BuildingController: Object.assign(BuildingController, BuildingController),
    FeedbackController: Object.assign(FeedbackController, FeedbackController),
    NeedController: Object.assign(NeedController, NeedController),
    AdminController: Object.assign(AdminController, AdminController),
    UserController: Object.assign(UserController, UserController),
}

export default Controllers