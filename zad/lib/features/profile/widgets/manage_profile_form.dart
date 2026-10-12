import 'package:flutter/material.dart';

class ManageProfileForm extends StatefulWidget {
  const ManageProfileForm({super.key});

  @override
  State<ManageProfileForm> createState() => _ManageProfileFormState();
}

class _ManageProfileFormState extends State<ManageProfileForm> {
  final _formKey = GlobalKey<FormState>();

  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Column(
        children: [
          _input(
            controller: _nameCtrl,
            label: 'Full Name',
            validator: (v) =>
            v == null || v.isEmpty ? 'Name is required' : null,
          ),
          const SizedBox(height: 16),
          _input(
            controller: _emailCtrl,
            label: 'Email',
            keyboard: TextInputType.emailAddress,
            validator: (v) =>
            v == null || !v.contains('@') ? 'Invalid email' : null,
          ),
          const SizedBox(height: 16),
          _input(
            controller: _phoneCtrl,
            label: 'Phone',
            keyboard: TextInputType.phone,
            validator: (v) =>
            v == null || v.isEmpty ? 'Phone is required' : null,
          ),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () {
                if (!_formKey.currentState!.validate()) return;

                // 🔹 مؤقت: فقط تأكيد التدفق
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Saved (UI only)')),
                );
              },
              child: const Text('Save'),
            ),
          ),
        ],
      ),
    );
  }

  Widget _input({
    required TextEditingController controller,
    required String label,
    TextInputType keyboard = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboard,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        border: const OutlineInputBorder(),
      ),
    );
  }
}

