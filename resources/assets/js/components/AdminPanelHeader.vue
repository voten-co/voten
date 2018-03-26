<template>
	<el-menu default-active="/big-daddy"
	         mode="horizontal"
	         router>
		<el-menu-item index="/big-daddy">Dashboard</el-menu-item>
		<el-menu-item index="/big-daddy/submissions">Submissions</el-menu-item>
		<el-menu-item index="/big-daddy/users">Users</el-menu-item>
		<el-menu-item index="/big-daddy/comments">Comments</el-menu-item>
		<el-menu-item index="/big-daddy/statistics">Statistics</el-menu-item>
		
		<el-submenu index="/big-daddy/channels">
			<template slot="title">Channels</template>

			<el-menu-item index="/big-daddy/channels">Index</el-menu-item>
			<el-menu-item index="/big-daddy/channels/suggested">Suggested</el-menu-item>
			<el-menu-item index="/big-daddy/channels/inactive">Inactive</el-menu-item>
		</el-submenu>
	</el-menu>
</template>
