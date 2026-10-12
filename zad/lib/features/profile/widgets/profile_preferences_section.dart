import 'package:flutter/material.dart';
import 'profile_menu_item.dart';

class ProfilePreferencesSection extends StatelessWidget {
  const ProfilePreferencesSection({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Preferences & Support',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 12),

        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            children: const [
              ProfileMenuItem(
                icon: Icons.brightness_6_outlined,
                title: 'Theme',
                trailingText: 'Light',
              ),

              ProfileMenuItem(
                icon: Icons.info_outline,
                title: 'About Us',
              ),

              ProfileMenuItem(
                icon: Icons.help_outline,
                title: 'Support',
              ),

            ],
          ),
        ),
      ],
    );
  }
}

