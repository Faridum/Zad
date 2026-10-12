import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../auth/providers/user_profile_provider.dart';
import '../providers/update_profile_provider.dart';

class ManageProfilePage extends ConsumerStatefulWidget {
  const ManageProfilePage({super.key});

  @override
  ConsumerState<ManageProfilePage> createState() =>
      _ManageProfilePageState();
}

class _ManageProfilePageState extends ConsumerState<ManageProfilePage> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController nameCtrl;
  late TextEditingController emailCtrl;
  late TextEditingController phoneCtrl;

  @override
  void initState() {
    super.initState();

    final user = ref.read(userProfileProvider).value;

    nameCtrl = TextEditingController(text: user?['name'] ?? '');
    emailCtrl = TextEditingController(text: user?['email'] ?? '');
    phoneCtrl = TextEditingController(text: user?['phone'] ?? '');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manage Profile')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                controller: nameCtrl,
                decoration: const InputDecoration(labelText: 'Name'),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: emailCtrl,
                decoration: const InputDecoration(labelText: 'Email'),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: phoneCtrl,
                decoration: const InputDecoration(labelText: 'Phone'),
              ),
              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: _save,
                child: const Text('Save'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    final data = {
      'name': nameCtrl.text,
      'email': emailCtrl.text,
      'phone': phoneCtrl.text,
    };

    try {
      await ref.read(updateProfileProvider(data).future);

      // إعادة جلب البروفايل
      ref.invalidate(userProfileProvider);

      if (!mounted) return;
      Navigator.pop(context);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString())),
      );
    }
  }
}

