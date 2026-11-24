<x-app-layout title="Audit Log · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Administration</span><h1 class="page-title">Security audit log</h1></div></div>
    <div class="table-wrap"><table><thead><tr><th>Time</th><th>Actor</th><th>Action</th><th>Subject</th><th>Details</th></tr></thead><tbody>
        @foreach ($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                <td class="table-cell-wrap">{{ $log->user->email ?? 'System' }}</td>
                <td><span class="audit-action">{{ $log->action_label }}</span></td>
                <td class="table-cell-wrap">{{ $log->subject_type ? class_basename($log->subject_type) : '—' }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}</td>
                <td class="audit-details">
                    @if (count($log->formatted_details))
                        <dl>
                            @foreach ($log->formatted_details as $detail)
                                <div><dt>{{ $detail['label'] }}</dt><dd>{{ $detail['value'] }}</dd></div>
                            @endforeach
                        </dl>
                    @else
                        <span class="audit-details-empty">—</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</x-app-layout>
