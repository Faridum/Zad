import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../auth/providers/user_profile_provider.dart';
import '../widgets/profile_header.dart';
import '../widgets/profile_account_section.dart';
import '../widgets/profile_preferences_section.dart';

import '../widgets/profile_logout_section.dart';

class ProfilePage extends ConsumerWidget {
  const ProfilePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profileAsync = ref.watch(userProfileProvider);

    return SafeArea(
      child: profileAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (_, __) => const Center(
          child: Text('Failed to load profile'),
        ),
        data: (user) {
          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Text(
                    'Profile',
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                const SizedBox(height: 24),

                ProfileHeader(
                  name: user['name'],
                  email: user['email'],
                ),

                const SizedBox(height: 24),
                ProfileAccountSection(user: user),

                const SizedBox(height: 24),
                const ProfilePreferencesSection(),



                const SizedBox(height: 32),
                const ProfileLogoutSection(),
              ],
            ),
          );
        },
      ),
    );
  }
}
